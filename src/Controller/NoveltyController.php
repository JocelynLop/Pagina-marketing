<?php

namespace App\Controller;

use App\Service\RestaurantDataStore;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class NoveltyController extends AbstractController
{
    #[Route('/novedades', name: 'app_novelties')]
    public function index(RestaurantDataStore $store): Response
    {
        $novelties = $store->novelties(true);

        foreach ($novelties as &$novelty) {
            $novelty['comments'] = $store->noveltyComments((string) $novelty['id']);
        }

        unset($novelty);

        return $this->render('novelties/index.html.twig', [
            'novelties' => $novelties,
        ]);
    }

    #[Route('/novedades/{id}/comentarios', name: 'app_novelty_comment', methods: ['POST'])]
    public function comment(string $id, Request $request, RestaurantDataStore $store): RedirectResponse
    {
        if (!$this->getUser()) {
            $this->addFlash('warning', 'Inicia sesion o crea una cuenta para comentar.');

            return $this->redirectToRoute('app_login');
        }

        if (!$this->isCsrfTokenValid('comment_novelty_'.$id, (string) $request->request->get('_token'))) {
            $this->addFlash('danger', 'No se pudo validar el comentario.');

            return $this->redirect($request->headers->get('referer') ?: $this->generateUrl('app_novelties'));
        }

        $content = trim((string) $request->request->get('content'));

        if ($content !== '') {
            $store->addNoveltyComment($id, $this->getUser()->getUserIdentifier(), $content);
            $this->addFlash('success', 'Comentario publicado.');
        }

        return $this->redirect($request->headers->get('referer') ?: $this->generateUrl('app_novelties'));
    }

    #[Route('/usuario/novedades', name: 'app_admin_novelties', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function manage(Request $request, RestaurantDataStore $store): Response
    {
        $errors = [];

        if ($request->isMethod('POST')) {
            $errors = $this->validateForm($request, 'create_novelty');

            if ($errors === []) {
                $novelties = $store->novelties();
                array_unshift($novelties, $this->payloadFromRequest($request, bin2hex(random_bytes(8))));
                $store->saveNovelties($novelties);
                $this->addFlash('success', 'Novedad creada correctamente.');

                return $this->redirectToRoute('app_admin_novelties');
            }
        }

        return $this->render('novelties/manage.html.twig', [
            'novelties' => $store->novelties(),
            'errors' => $errors,
            'active' => 'novelties',
        ]);
    }

    #[Route('/usuario/novedades/{id}/editar', name: 'app_admin_novelty_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(string $id, Request $request, RestaurantDataStore $store): Response
    {
        $novelties = $store->novelties();
        $index = $this->findIndex($novelties, $id);

        if ($index === null) {
            throw $this->createNotFoundException('Novedad no encontrada.');
        }

        $errors = [];
        $novelty = $novelties[$index];

        if ($request->isMethod('POST')) {
            $errors = $this->validateForm($request, 'edit_novelty_'.$id);

            if ($errors === []) {
                $novelties[$index] = $this->payloadFromRequest($request, $id, (string) ($novelty['createdAt'] ?? 'now'));
                $store->saveNovelties($novelties);
                $this->addFlash('success', 'Novedad actualizada.');

                return $this->redirectToRoute('app_admin_novelties');
            }
        }

        return $this->render('novelties/edit.html.twig', [
            'novelty' => $novelty,
            'errors' => $errors,
            'active' => 'novelties',
        ]);
    }

    #[Route('/usuario/novedades/{id}/eliminar', name: 'app_admin_novelty_delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(string $id, Request $request, RestaurantDataStore $store): RedirectResponse
    {
        if (!$this->isCsrfTokenValid('delete_novelty_'.$id, (string) $request->request->get('_token'))) {
            $this->addFlash('danger', 'No se pudo validar la solicitud.');

            return $this->redirectToRoute('app_admin_novelties');
        }

        $store->saveNovelties(array_values(array_filter(
            $store->novelties(),
            static fn (array $novelty): bool => ($novelty['id'] ?? '') !== $id
        )));
        $this->addFlash('success', 'Novedad eliminada.');

        return $this->redirectToRoute('app_admin_novelties');
    }

    #[Route('/publicaciones', name: 'app_publications_all')]
    public function oldAll(): RedirectResponse
    {
        return $this->redirectToRoute('app_novelties');
    }

    #[Route('/usuario/publicaciones', name: 'app_publications_manage')]
    #[IsGranted('ROLE_ADMIN')]
    public function oldManage(): RedirectResponse
    {
        return $this->redirectToRoute('app_admin_novelties');
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
     * @return list<string>
     */
    private function validateForm(Request $request, string $tokenId): array
    {
        $errors = [];

        if (!$this->isCsrfTokenValid($tokenId, (string) $request->request->get('_token'))) {
            $errors[] = 'La sesion expiro. Vuelve a intentarlo.';
        }

        if (trim((string) $request->request->get('title')) === '') {
            $errors[] = 'Agrega un titulo.';
        }

        if (trim((string) $request->request->get('summary')) === '') {
            $errors[] = 'Agrega un resumen.';
        }

        if (trim((string) $request->request->get('content')) === '') {
            $errors[] = 'Agrega el contenido.';
        }

        $imageUrl = trim((string) $request->request->get('imageUrl'));
        if ($imageUrl !== '' && !str_starts_with($imageUrl, '/') && !filter_var($imageUrl, FILTER_VALIDATE_URL)) {
            $errors[] = 'La imagen debe ser una URL valida o una ruta local.';
        }

        return $errors;
    }

    /**
     * @return array<string, mixed>
     */
    private function payloadFromRequest(Request $request, string $id, ?string $createdAt = null): array
    {
        return [
            'id' => $id,
            'title' => trim((string) $request->request->get('title')),
            'category' => trim((string) $request->request->get('category')) ?: 'General',
            'summary' => trim((string) $request->request->get('summary')),
            'content' => trim((string) $request->request->get('content')),
            'imageUrl' => trim((string) $request->request->get('imageUrl')),
            'customLabel' => trim((string) $request->request->get('customLabel')),
            'labelExpiresAt' => trim((string) $request->request->get('labelExpiresAt')),
            'expiresAt' => trim((string) $request->request->get('expiresAt')),
            'createdAt' => $createdAt ?? (new \DateTimeImmutable())->format('Y-m-d H:i:s'),
        ];
    }
}
