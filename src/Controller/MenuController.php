<?php

namespace App\Controller;

use App\Service\RestaurantDataStore;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class MenuController extends AbstractController
{
    #[Route('/menu', name: 'app_menu')]
    public function index(Request $request, RestaurantDataStore $store): Response
    {
        $items = $store->menuItems();
        $visitorId = $this->visitorId($request);
        $reactions = $store->menuReactions();

        foreach ($items as &$item) {
            $item['starredByViewer'] = in_array($visitorId, $reactions[$item['id']] ?? [], true);
        }

        unset($item);

        $categoryGroups = $this->groupMenuItemsByCategory($items, $store->menuCategories(), $store->menuCategoryTypes());

        return $this->render('menu/index.html.twig', [
            'items' => $items,
            'categoryGroups' => $categoryGroups,
            'menuCategoryTypes' => $store->menuCategoryTypes(),
            'activeType' => ((int) (new \DateTimeImmutable())->format('G')) < 13 ? 'morning' : 'afternoon',
        ]);
    }

    #[Route('/menu/{id}/estrella', name: 'app_menu_star', methods: ['POST'])]
    public function star(string $id, Request $request, RestaurantDataStore $store): Response
    {
        if (!$this->isCsrfTokenValid('star_menu_'.$id, (string) $request->request->get('_token'))) {
            if ($this->wantsJson($request)) {
                return new JsonResponse(['error' => 'No se pudo validar la reaccion.'], Response::HTTP_BAD_REQUEST);
            }

            $this->addFlash('danger', 'No se pudo validar la reaccion.');

            return $this->redirectToRoute('app_menu');
        }

        $active = $store->toggleMenuReaction($id, $this->visitorId($request));

        if ($this->wantsJson($request)) {
            $count = count($store->menuReactions()[$id] ?? []);

            return new JsonResponse([
                'active' => $active,
                'count' => $count,
                'label' => sprintf('%d estrella%s', $count, $count === 1 ? '' : 's'),
                'iconActive' => 'bi-star-fill',
                'iconInactive' => 'bi-star',
            ]);
        }

        return $this->redirect($request->headers->get('referer') ?: $this->generateUrl('app_menu'));
    }

    #[Route('/usuario/menu', name: 'app_admin_menu', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function manage(Request $request, RestaurantDataStore $store): Response
    {
        $errors = [];

        if ($request->isMethod('POST')) {
            $errors = $this->validateForm($request, 'create_menu_item', $store->menuCategories(), $store->menuCategoryTypes());

            if ($errors === []) {
                $items = $store->menuItems();
                array_unshift($items, $this->payloadFromRequest($request, bin2hex(random_bytes(8))));
                $store->saveMenuItems($items);
                $this->addFlash('success', 'Platillo agregado correctamente.');

                return $this->redirectToRoute('app_admin_menu');
            }
        }

        return $this->render('menu/manage.html.twig', [
            'items' => $store->menuItems(),
            'menuCategories' => $store->menuCategories(),
            'menuCategoryTypes' => $store->menuCategoryTypes(),
            'errors' => $errors,
            'active' => 'menu',
        ]);
    }

    #[Route('/usuario/menu/{id}/editar', name: 'app_admin_menu_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(string $id, Request $request, RestaurantDataStore $store): Response
    {
        $items = $store->menuItems();
        $index = $this->findIndex($items, $id);

        if ($index === null) {
            throw $this->createNotFoundException('Platillo no encontrado.');
        }

        $errors = [];
        $item = $items[$index];

        if ($request->isMethod('POST')) {
            $errors = $this->validateForm($request, 'edit_menu_item_'.$id, $store->menuCategories(), $store->menuCategoryTypes());

            if ($errors === []) {
                $items[$index] = $this->payloadFromRequest($request, $id);
                $store->saveMenuItems($items);
                $this->addFlash('success', 'Platillo actualizado.');

                return $this->redirectToRoute('app_admin_menu');
            }
        }

        return $this->render('menu/edit.html.twig', [
            'item' => $item,
            'menuCategories' => $store->menuCategories(),
            'menuCategoryTypes' => $store->menuCategoryTypes(),
            'errors' => $errors,
            'active' => 'menu',
        ]);
    }

    #[Route('/usuario/menu/{id}/eliminar', name: 'app_admin_menu_delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(string $id, Request $request, RestaurantDataStore $store): RedirectResponse
    {
        if (!$this->isCsrfTokenValid('delete_menu_item_'.$id, (string) $request->request->get('_token'))) {
            $this->addFlash('danger', 'No se pudo validar la solicitud.');

            return $this->redirectToRoute('app_admin_menu');
        }

        $store->saveMenuItems(array_values(array_filter(
            $store->menuItems(),
            static fn (array $item): bool => ($item['id'] ?? '') !== $id
        )));
        $this->addFlash('success', 'Platillo eliminado.');

        return $this->redirectToRoute('app_admin_menu');
    }

    private function visitorId(Request $request): string
    {
        if ($this->getUser()) {
            return 'user:'.$this->getUser()->getUserIdentifier();
        }

        $session = $request->getSession();
        if (!$session->isStarted()) {
            $session->start();
        }

        if (!$session->has('visitor_id')) {
            $session->set('visitor_id', 'guest:'.bin2hex(random_bytes(12)));
        }

        return (string) $session->get('visitor_id');
    }

    private function wantsJson(Request $request): bool
    {
        return $request->isXmlHttpRequest() || str_contains((string) $request->headers->get('Accept'), 'application/json');
    }

    /**
     * @param array<int, array<string, mixed>> $items
     */
    private function findIndex(array $items, string $id): ?int
    {
        foreach ($items as $index => $item) {
            if (($item['id'] ?? '') === $id) {
                return $index;
            }
        }

        return null;
    }

    /**
     * @param array<int, array<string, mixed>> $items
     * @param array<string, array{label: string, categories: array<string, string>}> $categories
     *
     * @return array<int, array{label: string, categories: array<int, array{label: string, items: array<int, array<string, mixed>>}>}>
     */
    private function groupMenuItemsByCategory(array $items, array $categories, array $categoryTypes): array
    {
        $groups = [];

        foreach ($categories as $groupKey => $group) {
            $categoryItems = [];

            foreach ($group['categories'] as $categoryKey => $label) {
                $matches = array_values(array_filter(
                    $items,
                    static fn (array $item): bool => ($item['category'] ?? '') === $categoryKey
                ));

                if ($matches === []) {
                    continue;
                }

                $categoryItems[] = [
                    'key' => $categoryKey,
                    'label' => $label,
                    'types' => $categoryTypes[$categoryKey] ?? ['morning', 'afternoon'],
                    'items' => $matches,
                ];
            }

            if ($categoryItems === []) {
                continue;
            }

            $groups[] = [
                'label' => $group['label'],
                'categories' => $categoryItems,
            ];
        }

        return $groups;
    }

    /**
     * @return list<string>
     */
    private function validateForm(Request $request, string $tokenId, array $menuCategories, array $menuCategoryTypes): array
    {
        $errors = [];

        if (!$this->isCsrfTokenValid($tokenId, (string) $request->request->get('_token'))) {
            $errors[] = 'La sesion expiro. Vuelve a intentarlo.';
        }

        foreach (['name' => 'nombre', 'description' => 'descripcion', 'price' => 'precio'] as $field => $label) {
            if (trim((string) $request->request->get($field)) === '') {
                $errors[] = sprintf('Agrega el %s del platillo.', $label);
            }
        }

        if (!in_array((string) $request->request->get('type'), ['morning', 'afternoon'], true)) {
            $errors[] = 'Selecciona el tipo de menu.';
        }

        $category = (string) $request->request->get('category');
        $validCategories = array_merge(...array_map(
            static fn (array $group): array => array_keys($group['categories']),
            $menuCategories
        ));

        if (!in_array($category, $validCategories, true)) {
            $errors[] = 'Selecciona una categoria valida.';
        }

        $type = (string) $request->request->get('type');
        if ($category !== '' && isset($menuCategoryTypes[$category]) && !in_array($type, $menuCategoryTypes[$category], true)) {
            $errors[] = 'La categoria seleccionada no pertenece al tipo de menu elegido.';
        }

        return $errors;
    }

    /**
     * @return array<string, mixed>
     */
    private function payloadFromRequest(Request $request, string $id): array
    {
        return [
            'id' => $id,
            'name' => trim((string) $request->request->get('name')),
            'description' => trim((string) $request->request->get('description')),
            'price' => (float) $request->request->get('price'),
            'type' => (string) $request->request->get('type'),
            'category' => (string) $request->request->get('category'),
            'imageUrl' => trim((string) $request->request->get('imageUrl')),
        ];
    }
}
