<?php

namespace App\Controller;

use App\Service\RestaurantDataStore;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class RestaurantController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(RestaurantDataStore $store): Response
    {
        $heroItems = $store->topMenuItems();
        $menuItems = $store->menuItems();
        $morningItems = $this->menuItemsByType($menuItems, 'morning');
        $afternoonItems = $this->menuItemsByType($menuItems, 'afternoon');
        $morningDishItems = $this->menuItemsExcludingCategories($morningItems, ['cafes', 'jugos', 'cocteleria', 'licores', 'cervezas', 'sodas-italianas', 'aguas-refrescos']);
        $afternoonCutItems = $this->menuItemsInCategories($afternoonItems, ['cortes-americanos', 'cortes-importados']);
        $novelties = array_slice($store->novelties(true), 0, 3);
        $blogPosts = array_slice($store->blogPosts(), 0, 3);

        foreach ($novelties as &$novelty) {
            $novelty['comments'] = $store->noveltyComments((string) $novelty['id']);
        }

        foreach ($blogPosts as &$post) {
            $post['comments'] = $store->blogComments((string) $post['id']);
        }

        unset($novelty, $post);

        return $this->render('restaurant/index.html.twig', [
            'contact' => $store->contact(),
            'heroItems' => $heroItems,
            'heroStars' => array_sum(array_map(static fn (array $item): int => (int) ($item['stars'] ?? 0), $heroItems)),
            'menuHighlights' => $store->topMenuItems(5),
            'morningItems' => $morningItems,
            'afternoonItems' => $afternoonItems,
            'morningDishItems' => $morningDishItems,
            'afternoonCutItems' => $afternoonCutItems,
            'novelties' => $novelties,
            'blogPosts' => $blogPosts,
        ]);
    }

    /**
     * @param array<int, array<string, mixed>> $items
     *
     * @return array<int, array<string, mixed>>
     */
    private function menuItemsByType(array $items, string $type): array
    {
        $items = array_values(array_filter(
            $items,
            static fn (array $item): bool => ($item['type'] ?? '') === $type
        ));

        usort($items, static function (array $first, array $second): int {
            $stars = ((int) ($second['stars'] ?? 0)) <=> ((int) ($first['stars'] ?? 0));

            return $stars !== 0 ? $stars : strcmp((string) ($first['name'] ?? ''), (string) ($second['name'] ?? ''));
        });

        return $items;
    }

    /**
     * @param array<int, array<string, mixed>> $items
     * @param array<int, string> $categories
     *
     * @return array<int, array<string, mixed>>
     */
    private function menuItemsInCategories(array $items, array $categories): array
    {
        return array_values(array_filter(
            $items,
            static fn (array $item): bool => in_array((string) ($item['category'] ?? ''), $categories, true)
        ));
    }

    /**
     * @param array<int, array<string, mixed>> $items
     * @param array<int, string> $categories
     *
     * @return array<int, array<string, mixed>>
     */
    private function menuItemsExcludingCategories(array $items, array $categories): array
    {
        return array_values(array_filter(
            $items,
            static fn (array $item): bool => !in_array((string) ($item['category'] ?? ''), $categories, true)
        ));
    }

    #[Route('/usuario/contacto', name: 'app_admin_contact', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function contact(Request $request, RestaurantDataStore $store): Response
    {
        $errors = [];
        $contact = $store->contact();

        if ($request->isMethod('POST')) {
            if (!$this->isCsrfTokenValid('save_contact', (string) $request->request->get('_token'))) {
                $errors[] = 'La sesion expiro. Vuelve a intentarlo.';
            }

            $contact = [
                'phone' => trim((string) $request->request->get('phone')),
                'whatsapp' => trim((string) $request->request->get('whatsapp')),
                'address' => trim((string) $request->request->get('address')),
                'mapsUrl' => trim((string) $request->request->get('mapsUrl')),
                'facebookUrl' => trim((string) $request->request->get('facebookUrl')),
                'email' => trim((string) $request->request->get('email')),
                'message' => trim((string) $request->request->get('message')),
            ];

            if ($contact['phone'] === '') {
                $errors[] = 'Agrega el telefono principal.';
            }

            if ($contact['address'] === '') {
                $errors[] = 'Agrega la direccion del restaurante.';
            }

            foreach (['mapsUrl' => 'Google Maps', 'facebookUrl' => 'Facebook'] as $field => $label) {
                if ($contact[$field] !== '' && !filter_var($contact[$field], FILTER_VALIDATE_URL)) {
                    $errors[] = sprintf('El enlace de %s debe ser una URL valida.', $label);
                }
            }

            if ($errors === []) {
                $store->saveContact($contact);
                $this->addFlash('success', 'Contacto actualizado correctamente.');

                return $this->redirectToRoute('app_admin_contact');
            }
        }

        return $this->render('admin/contact.html.twig', [
            'contact' => $contact,
            'errors' => $errors,
            'active' => 'contact',
        ]);
    }
}
