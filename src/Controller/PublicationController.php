<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

final class PublicationController extends AbstractController
{
    private const STORAGE_FILE = '/data/publications.json';

    #[Route('/', name: 'app_publications')]
    public function index(): Response
    {
        return $this->render('publications/index.html.twig', [
            'publications' => $this->loadPublications(),
        ]);
    }

    #[Route('/usuario/publicaciones', name: 'app_publications_manage', methods: ['GET', 'POST'])]
    public function manage(Request $request, CsrfTokenManagerInterface $csrfTokenManager): Response
    {
        $errors = [];

        if ($request->isMethod('POST')) {
            $submittedToken = (string) $request->request->get('_token');

            if (!$csrfTokenManager->isTokenValid(new CsrfToken('create_publication', $submittedToken))) {
                $errors[] = 'La sesion expiro. Vuelve a intentarlo.';
            }

            $title = trim((string) $request->request->get('title'));
            $category = trim((string) $request->request->get('category'));
            $summary = trim((string) $request->request->get('summary'));
            $content = trim((string) $request->request->get('content'));
            $imageUrl = trim((string) $request->request->get('image_url'));

            if ($title === '') {
                $errors[] = 'Agrega un titulo para la publicacion.';
            }

            if ($summary === '') {
                $errors[] = 'Agrega un resumen breve.';
            }

            if ($content === '') {
                $errors[] = 'Agrega el contenido de la publicacion.';
            }

            if ($imageUrl !== '' && !filter_var($imageUrl, FILTER_VALIDATE_URL)) {
                $errors[] = 'La imagen debe ser una URL valida.';
            }

            if ($errors === []) {
                $publications = $this->loadPublications();
                array_unshift($publications, [
                    'id' => bin2hex(random_bytes(8)),
                    'title' => $title,
                    'category' => $category !== '' ? $category : 'General',
                    'summary' => $summary,
                    'content' => $content,
                    'imageUrl' => $imageUrl,
                    'createdAt' => (new \DateTimeImmutable())->format('Y-m-d H:i:s'),
                ]);

                $this->savePublications($publications);
                $this->addFlash('success', 'Publicacion creada correctamente.');

                return $this->redirectToRoute('app_publications_manage');
            }
        }

        return $this->render('publications/manage.html.twig', [
            'publications' => $this->loadPublications(),
            'errors' => $errors,
        ]);
    }

    #[Route('/usuario/publicaciones/{id}/eliminar', name: 'app_publications_delete', methods: ['POST'])]
    public function delete(string $id, Request $request, CsrfTokenManagerInterface $csrfTokenManager): RedirectResponse
    {
        $submittedToken = (string) $request->request->get('_token');

        if (!$csrfTokenManager->isTokenValid(new CsrfToken('delete_publication_'.$id, $submittedToken))) {
            $this->addFlash('danger', 'No se pudo validar la solicitud.');

            return $this->redirectToRoute('app_publications_manage');
        }

        $publications = array_values(array_filter(
            $this->loadPublications(),
            static fn (array $publication): bool => $publication['id'] !== $id
        ));

        $this->savePublications($publications);
        $this->addFlash('success', 'Publicacion eliminada.');

        return $this->redirectToRoute('app_publications_manage');
    }

    /**
     * @return array<int, array<string, string>>
     */
    private function loadPublications(): array
    {
        $path = $this->getStoragePath();

        if (!is_file($path)) {
            return $this->starterPublications();
        }

        $content = file_get_contents($path);
        $publications = json_decode($content ?: '[]', true);

        return is_array($publications) ? $publications : [];
    }

    /**
     * @param array<int, array<string, string>> $publications
     */
    private function savePublications(array $publications): void
    {
        $filesystem = new Filesystem();
        $path = $this->getStoragePath();
        $filesystem->mkdir(dirname($path));
        $filesystem->dumpFile($path, json_encode($publications, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    private function getStoragePath(): string
    {
        return $this->getParameter('kernel.project_dir').'/var'.self::STORAGE_FILE;
    }

    /**
     * @return array<int, array<string, string>>
     */
    private function starterPublications(): array
    {
        return [
            [
                'id' => 'welcome',
                'title' => 'Nueva coleccion de temporada',
                'category' => 'Novedades',
                'summary' => 'Descubre las ultimas propuestas, seleccionadas para crear una experiencia elegante y cercana.',
                'content' => 'Esta publicacion de ejemplo muestra como se vera el contenido para tus clientes. Desde el panel de usuario puedes crear nuevas publicaciones y estas apareceran automaticamente en la pagina principal.',
                'imageUrl' => 'https://images.unsplash.com/photo-1497215728101-856f4ea42174?auto=format&fit=crop&w=1200&q=80',
                'createdAt' => '2026-09-24 10:00:00',
            ],
        ];
    }
}
