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

final class BlogController extends AbstractController
{
    #[Route('/blog', name: 'app_blog')]
    public function index(RestaurantDataStore $store): Response
    {
        $posts = $store->blogPosts();

        foreach ($posts as &$post) {
            $post['comments'] = $store->blogComments((string) $post['id']);
        }

        unset($post);

        return $this->render('blog/index.html.twig', [
            'posts' => $posts,
        ]);
    }

    #[Route('/blog/{id}', name: 'app_blog_show', methods: ['GET'])]
    public function show(string $id, Request $request, RestaurantDataStore $store): Response
    {
        $post = $this->findById($store->blogPosts(), $id);

        if (!$post) {
            throw $this->createNotFoundException('Entrada no encontrada.');
        }

        $reactions = $store->blogReactions();
        $visitorId = $this->visitorId($request);
        $post['heartedByViewer'] = in_array($visitorId, $reactions[$id] ?? [], true);

        return $this->render('blog/show.html.twig', [
            'post' => $post,
            'comments' => $store->blogComments($id),
        ]);
    }

    #[Route('/blog/{id}/corazon', name: 'app_blog_heart', methods: ['POST'])]
    public function heart(string $id, Request $request, RestaurantDataStore $store): Response
    {
        if (!$this->isCsrfTokenValid('heart_blog_'.$id, (string) $request->request->get('_token'))) {
            if ($this->wantsJson($request)) {
                return new JsonResponse(['error' => 'No se pudo validar la reaccion.'], Response::HTTP_BAD_REQUEST);
            }

            $this->addFlash('danger', 'No se pudo validar la reaccion.');

            return $this->redirect($request->headers->get('referer') ?: $this->generateUrl('app_blog'));
        }

        $active = $store->toggleBlogReaction($id, $this->visitorId($request));

        if ($this->wantsJson($request)) {
            $count = count($store->blogReactions()[$id] ?? []);

            return new JsonResponse([
                'active' => $active,
                'count' => $count,
                'label' => sprintf('%d corazon%s', $count, $count === 1 ? '' : 'es'),
                'iconActive' => 'bi-heart-fill',
                'iconInactive' => 'bi-heart',
            ]);
        }

        return $this->redirect($request->headers->get('referer') ?: $this->generateUrl('app_blog'));
    }

    #[Route('/blog/{id}/comentarios', name: 'app_blog_comment', methods: ['POST'])]
    public function comment(string $id, Request $request, RestaurantDataStore $store): RedirectResponse
    {
        if (!$this->getUser()) {
            $this->addFlash('warning', 'Inicia sesion o crea una cuenta para comentar.');

            return $this->redirectToRoute('app_login');
        }

        if (!$this->isCsrfTokenValid('comment_blog_'.$id, (string) $request->request->get('_token'))) {
            $this->addFlash('danger', 'No se pudo validar el comentario.');

            return $this->redirect($request->headers->get('referer') ?: $this->generateUrl('app_blog'));
        }

        $content = trim((string) $request->request->get('content'));

        if ($content !== '') {
            $store->addBlogComment($id, $this->getUser()->getUserIdentifier(), $content);
            $this->addFlash('success', 'Comentario publicado.');
        }

        return $this->redirect($request->headers->get('referer') ?: $this->generateUrl('app_blog'));
    }

    #[Route('/usuario/blog', name: 'app_admin_blog', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function manage(Request $request, RestaurantDataStore $store): Response
    {
        $errors = [];

        if ($request->isMethod('POST')) {
            $errors = $this->validateForm($request, 'create_blog_post');

            if ($errors === []) {
                $posts = $store->blogPosts();
                array_unshift($posts, $this->payloadFromRequest($request, bin2hex(random_bytes(8))));
                $store->saveBlogPosts($posts);
                $this->addFlash('success', 'Entrada creada correctamente.');

                return $this->redirectToRoute('app_admin_blog');
            }
        }

        $posts = $store->blogPosts();

        return $this->render('blog/manage.html.twig', [
            'posts' => $posts,
            'heartTotal' => array_sum(array_map(static fn (array $post): int => (int) ($post['hearts'] ?? 0), $posts)),
            'comments' => $store->allBlogComments(),
            'errors' => $errors,
            'active' => 'blog',
        ]);
    }

    #[Route('/usuario/blog/{id}/editar', name: 'app_admin_blog_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(string $id, Request $request, RestaurantDataStore $store): Response
    {
        $posts = $store->blogPosts();
        $index = $this->findIndex($posts, $id);

        if ($index === null) {
            throw $this->createNotFoundException('Entrada no encontrada.');
        }

        $errors = [];
        $post = $posts[$index];

        if ($request->isMethod('POST')) {
            $errors = $this->validateForm($request, 'edit_blog_post_'.$id);

            if ($errors === []) {
                $posts[$index] = $this->payloadFromRequest($request, $id, (string) ($post['createdAt'] ?? 'now'));
                $store->saveBlogPosts($posts);
                $this->addFlash('success', 'Entrada actualizada.');

                return $this->redirectToRoute('app_admin_blog');
            }
        }

        return $this->render('blog/edit.html.twig', [
            'post' => $post,
            'errors' => $errors,
            'active' => 'blog',
        ]);
    }

    #[Route('/usuario/blog/{id}/eliminar', name: 'app_admin_blog_delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(string $id, Request $request, RestaurantDataStore $store): RedirectResponse
    {
        if (!$this->isCsrfTokenValid('delete_blog_post_'.$id, (string) $request->request->get('_token'))) {
            $this->addFlash('danger', 'No se pudo validar la solicitud.');

            return $this->redirectToRoute('app_admin_blog');
        }

        $store->saveBlogPosts(array_values(array_filter(
            $store->blogPosts(),
            static fn (array $post): bool => ($post['id'] ?? '') !== $id
        )));
        $this->addFlash('success', 'Entrada eliminada.');

        return $this->redirectToRoute('app_admin_blog');
    }

    #[Route('/usuario/blog/comentarios/{id}/eliminar', name: 'app_admin_blog_comment_delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function deleteComment(string $id, Request $request, RestaurantDataStore $store): RedirectResponse
    {
        if (!$this->isCsrfTokenValid('delete_blog_comment_'.$id, (string) $request->request->get('_token'))) {
            $this->addFlash('danger', 'No se pudo validar la solicitud.');

            return $this->redirectToRoute('app_admin_blog');
        }

        $store->deleteBlogComment($id);
        $this->addFlash('success', 'Comentario eliminado.');

        return $this->redirectToRoute('app_admin_blog');
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
     * @return array<string, mixed>|null
     */
    private function findById(array $items, string $id): ?array
    {
        $index = $this->findIndex($items, $id);

        return $index === null ? null : $items[$index];
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

        foreach (['title' => 'titulo', 'summary' => 'resumen', 'content' => 'contenido'] as $field => $label) {
            if (trim((string) $request->request->get($field)) === '') {
                $errors[] = sprintf('Agrega el %s de la entrada.', $label);
            }
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
            'summary' => trim((string) $request->request->get('summary')),
            'content' => trim((string) $request->request->get('content')),
            'imageUrl' => trim((string) $request->request->get('imageUrl')),
            'createdAt' => $createdAt ?? (new \DateTimeImmutable())->format('Y-m-d H:i:s'),
        ];
    }
}
