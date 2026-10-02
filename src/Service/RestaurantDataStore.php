<?php

namespace App\Service;

use Symfony\Component\Filesystem\Filesystem;

final class RestaurantDataStore
{
    private const MAPS_URL = 'https://www.google.com/maps/place/RESTAURANTE+LAS+TABLAS/@18.3664974,-95.7977665,17.4z/data=!4m6!3m5!1s0x85c3b5c173a009df:0x656b3de4dccd989e!8m2!3d18.3652129!4d-95.7958952!16s%2Fg%2F1tdw8d0q?entry=ttu&g_ep=EgoyMDI2MDkyMy4wIKXMDSoASAFQAw%3D%3D';
    private const MENU_CATEGORIES = [
        'platillos' => [
            'label' => 'Platillos',
            'categories' => [
                'entradas' => 'Entradas',
                'ensaladas' => 'Ensaladas',
                'sopas-cremas-pastas' => 'Sopas, cremas y pastas',
                'platillos-del-mar' => 'Platillos del mar',
                'aves' => 'Aves',
                'tacos-con-sin-queso' => 'Tacos con y sin queso',
                'burritos' => 'Burritos',
                'cortes-americanos' => 'Cortes americanos',
                'cortes-importados' => 'Cortes importados',
                'hamburguesas-baguette' => 'Hamburguesas y baguette',
            ],
        ],
        'bebidas' => [
            'label' => 'Bebidas',
            'categories' => [
                'cafes' => 'Cafes',
                'jugos' => 'Jugos',
                'cocteleria' => 'Cocteleria',
                'licores' => 'Licores',
                'cervezas' => 'Cervezas',
                'sodas-italianas' => 'Sodas italianas',
                'aguas-refrescos' => 'Aguas y refrescos',
            ],
        ],
        'postres' => [
            'label' => 'Postres',
            'categories' => [
                'postres' => 'Postres',
            ],
        ],
    ];
    private const MENU_CATEGORY_TYPES = [
        'entradas' => ['morning', 'afternoon'],
        'ensaladas' => ['afternoon'],
        'sopas-cremas-pastas' => ['afternoon'],
        'platillos-del-mar' => ['afternoon'],
        'aves' => ['afternoon'],
        'tacos-con-sin-queso' => ['afternoon'],
        'burritos' => ['afternoon'],
        'cortes-americanos' => ['afternoon'],
        'cortes-importados' => ['afternoon'],
        'hamburguesas-baguette' => ['afternoon'],
        'cafes' => ['morning'],
        'jugos' => ['morning'],
        'cocteleria' => ['afternoon'],
        'licores' => ['afternoon'],
        'cervezas' => ['afternoon'],
        'sodas-italianas' => ['afternoon'],
        'aguas-refrescos' => ['morning', 'afternoon'],
        'postres' => ['morning', 'afternoon'],
    ];

    public function __construct(private readonly string $projectDir)
    {
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function novelties(bool $publicOnly = false): array
    {
        $novelties = $this->readList('novelties.json', $this->starterNovelties());
        $now = new \DateTimeImmutable();

        $novelties = array_map(static function (array $novelty) use ($now): array {
            $labelExpiresAt = trim((string) ($novelty['labelExpiresAt'] ?? ''));
            $expiresAt = trim((string) ($novelty['expiresAt'] ?? ''));
            $novelty['isLabelActive'] = $labelExpiresAt === '' || new \DateTimeImmutable($labelExpiresAt) > $now;
            $novelty['isExpired'] = $expiresAt !== '' && new \DateTimeImmutable($expiresAt) <= $now;

            return $novelty;
        }, $novelties);

        if ($publicOnly) {
            $novelties = array_values(array_filter($novelties, static fn (array $novelty): bool => !($novelty['isExpired'] ?? false)));
        }

        usort($novelties, static fn (array $first, array $second): int => strtotime((string) ($second['createdAt'] ?? '')) <=> strtotime((string) ($first['createdAt'] ?? '')));

        return $novelties;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function menuItems(): array
    {
        $items = $this->readList('menu_items.json', $this->starterMenuItems());
        $reactions = $this->menuReactions();

        foreach ($items as &$item) {
            $category = (string) ($item['category'] ?? '');
            if (!$this->isKnownMenuCategory($category)) {
                $category = $this->defaultCategoryForItem($item);
            }

            $item['category'] = $category;
            $item['categoryLabel'] = $this->menuCategoryLabel($category);
            $item['categoryGroup'] = $this->menuCategoryGroup($category);
            $item['categoryGroupLabel'] = $this->menuCategoryGroupLabel($category);
            $item['stars'] = count($reactions[$item['id']] ?? []);
        }

        unset($item);

        return $items;
    }

    /**
     * @return array<string, array{label: string, categories: array<string, string>}>
     */
    public function menuCategories(): array
    {
        return self::MENU_CATEGORIES;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function menuCategoryTypes(): array
    {
        return self::MENU_CATEGORY_TYPES;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function menuReactions(): array
    {
        return $this->readMap('menu_reactions.json', []);
    }

    public function toggleMenuReaction(string $itemId, string $visitorId): bool
    {
        $reactions = $this->menuReactions();
        $itemReactions = $reactions[$itemId] ?? [];

        if (in_array($visitorId, $itemReactions, true)) {
            $reactions[$itemId] = array_values(array_filter($itemReactions, static fn (string $id): bool => $id !== $visitorId));
            $this->write('menu_reactions.json', $reactions);

            return false;
        }

        $itemReactions[] = $visitorId;
        $reactions[$itemId] = array_values(array_unique($itemReactions));
        $this->write('menu_reactions.json', $reactions);

        return true;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function topMenuItems(int $limit = 4): array
    {
        $items = $this->menuItems();
        usort($items, static function (array $first, array $second): int {
            $stars = ($second['stars'] ?? 0) <=> ($first['stars'] ?? 0);

            return $stars !== 0 ? $stars : strcmp((string) $first['name'], (string) $second['name']);
        });

        return array_slice($items, 0, $limit);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function blogPosts(): array
    {
        $posts = $this->readList('blog_posts.json', $this->starterBlogPosts());
        $reactions = $this->blogReactions();

        foreach ($posts as &$post) {
            $post['hearts'] = count($reactions[$post['id']] ?? []);
        }

        unset($post);
        usort($posts, static fn (array $first, array $second): int => strtotime((string) ($second['createdAt'] ?? '')) <=> strtotime((string) ($first['createdAt'] ?? '')));

        return $posts;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function blogComments(string $postId): array
    {
        $comments = $this->readList('blog_comments.json', []);
        $comments = array_values(array_filter($comments, static fn (array $comment): bool => ($comment['postId'] ?? '') === $postId));
        usort($comments, static fn (array $first, array $second): int => strtotime((string) ($first['createdAt'] ?? '')) <=> strtotime((string) ($second['createdAt'] ?? '')));

        return $comments;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function allBlogComments(): array
    {
        $comments = $this->readList('blog_comments.json', []);
        usort($comments, static fn (array $first, array $second): int => strtotime((string) ($second['createdAt'] ?? '')) <=> strtotime((string) ($first['createdAt'] ?? '')));

        return $comments;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function blogReactions(): array
    {
        return $this->readMap('blog_reactions.json', []);
    }

    public function toggleBlogReaction(string $postId, string $visitorId): bool
    {
        $reactions = $this->blogReactions();
        $postReactions = $reactions[$postId] ?? [];

        if (in_array($visitorId, $postReactions, true)) {
            $reactions[$postId] = array_values(array_filter($postReactions, static fn (string $id): bool => $id !== $visitorId));
            $this->write('blog_reactions.json', $reactions);

            return false;
        }

        $postReactions[] = $visitorId;
        $reactions[$postId] = array_values(array_unique($postReactions));
        $this->write('blog_reactions.json', $reactions);

        return true;
    }

    public function addBlogComment(string $postId, string $author, string $content): void
    {
        $comments = $this->readList('blog_comments.json', []);
        $comments[] = [
            'id' => bin2hex(random_bytes(8)),
            'postId' => $postId,
            'author' => $author,
            'content' => $content,
            'createdAt' => (new \DateTimeImmutable())->format('Y-m-d H:i:s'),
        ];

        $this->write('blog_comments.json', $comments);
    }

    public function deleteBlogComment(string $commentId): void
    {
        $comments = array_values(array_filter(
            $this->readList('blog_comments.json', []),
            static fn (array $comment): bool => ($comment['id'] ?? '') !== $commentId
        ));

        $this->write('blog_comments.json', $comments);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function noveltyComments(string $noveltyId): array
    {
        $comments = $this->readList('novelty_comments.json', []);
        $comments = array_values(array_filter($comments, static fn (array $comment): bool => ($comment['noveltyId'] ?? '') === $noveltyId));
        usort($comments, static fn (array $first, array $second): int => strtotime((string) ($first['createdAt'] ?? '')) <=> strtotime((string) ($second['createdAt'] ?? '')));

        return $comments;
    }

    public function addNoveltyComment(string $noveltyId, string $author, string $content): void
    {
        $comments = $this->readList('novelty_comments.json', []);
        $comments[] = [
            'id' => bin2hex(random_bytes(8)),
            'noveltyId' => $noveltyId,
            'author' => $author,
            'content' => $content,
            'createdAt' => (new \DateTimeImmutable())->format('Y-m-d H:i:s'),
        ];

        $this->write('novelty_comments.json', $comments);
    }

    /**
     * @return array<string, string>
     */
    public function contact(): array
    {
        return $this->readMap('site_contact.json', $this->starterContact());
    }

    /**
     * @param array<string, string> $contact
     */
    public function saveContact(array $contact): void
    {
        $this->write('site_contact.json', $contact);
    }

    /**
     * @param array<int, array<string, mixed>> $novelties
     */
    public function saveNovelties(array $novelties): void
    {
        $this->write('novelties.json', $novelties);
    }

    /**
     * @param array<int, array<string, mixed>> $items
     */
    public function saveMenuItems(array $items): void
    {
        $this->write('menu_items.json', $items);
    }

    /**
     * @param array<int, array<string, mixed>> $posts
     */
    public function saveBlogPosts(array $posts): void
    {
        $this->write('blog_posts.json', $posts);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function readList(string $file, array $fallback): array
    {
        $data = $this->read($file, $fallback);

        return is_array($data) ? array_values($data) : $fallback;
    }

    /**
     * @return array<string, mixed>
     */
    private function readMap(string $file, array $fallback): array
    {
        $data = $this->read($file, $fallback);

        return is_array($data) ? $data : $fallback;
    }

    /**
     * @return mixed
     */
    private function read(string $file, mixed $fallback): mixed
    {
        $path = $this->path($file);

        if (!is_file($path)) {
            return $fallback;
        }

        $content = file_get_contents($path);
        $data = json_decode($content ?: '[]', true);

        return $data ?? $fallback;
    }

    private function write(string $file, mixed $data): void
    {
        $filesystem = new Filesystem();
        $path = $this->path($file);
        $filesystem->mkdir(dirname($path));
        $filesystem->dumpFile($path, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    private function path(string $file): string
    {
        return $this->projectDir.'/var/data/'.$file;
    }

    private function isKnownMenuCategory(string $category): bool
    {
        foreach (self::MENU_CATEGORIES as $group) {
            if (array_key_exists($category, $group['categories'])) {
                return true;
            }
        }

        return false;
    }

    private function menuCategoryLabel(string $category): string
    {
        foreach (self::MENU_CATEGORIES as $group) {
            if (isset($group['categories'][$category])) {
                return $group['categories'][$category];
            }
        }

        return 'Platillos';
    }

    private function menuCategoryGroup(string $category): string
    {
        foreach (self::MENU_CATEGORIES as $groupKey => $group) {
            if (array_key_exists($category, $group['categories'])) {
                return $groupKey;
            }
        }

        return 'platillos';
    }

    private function menuCategoryGroupLabel(string $category): string
    {
        $group = $this->menuCategoryGroup($category);

        return self::MENU_CATEGORIES[$group]['label'] ?? 'Platillos';
    }

    /**
     * @param array<string, mixed> $item
     */
    private function defaultCategoryForItem(array $item): string
    {
        $name = strtolower((string) ($item['name'] ?? ''));
        $imageUrl = strtolower((string) ($item['imageUrl'] ?? ''));
        $text = $name.' '.$imageUrl;

        if (str_contains($text, 'capuchino') || str_contains($text, 'cafe') || str_contains($text, 'lechero')) {
            return 'cafes';
        }

        if (str_contains($text, 'aperol') || str_contains($text, 'coctel') || str_contains($text, 'mezcal')) {
            return 'cocteleria';
        }

        if (str_contains($text, 'pulpo') || str_contains($text, 'salmon')) {
            return 'platillos-del-mar';
        }

        if (str_contains($text, 'hamburguesa') || str_contains($text, 'baguette')) {
            return 'hamburguesas-baguette';
        }

        if (str_contains($text, 'taco')) {
            return 'tacos-con-sin-queso';
        }

        if (str_contains($text, 'rib eye') || str_contains($text, 'tomahawk') || str_contains($text, 't bone')) {
            return 'cortes-importados';
        }

        if (str_contains($text, 'tampiquena') || str_contains($text, 'tampiqueña') || str_contains($text, 'beef')) {
            return 'cortes-americanos';
        }

        return 'entradas';
    }

    /**
     * @return array<string, string>
     */
    private function starterContact(): array
    {
        return [
            'phone' => '2886904446',
            'whatsapp' => '',
            'address' => 'Adolfo, Ruiz Cortines 408, Centro, Cosamaloapan, Ver.',
            'mapsUrl' => self::MAPS_URL,
            'facebookUrl' => 'https://www.facebook.com/share/17YSUBgsC2/',
            'email' => '',
            'message' => 'Estamos listos para recibirte con platillos de parrilla, desayunos y sabores hechos para compartirse.',
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function starterNovelties(): array
    {
        return [
            [
                'id' => 'cafe-con-pan',
                'title' => 'Cafe con pan para iniciar el dia',
                'category' => 'Promocion',
                'summary' => 'Una opcion calida para acompanar tu manana en Las Tablas.',
                'content' => 'Disfruta un cafe recien servido con pan dulce y empieza el dia con ese sabor casero que se antoja desde temprano.',
                'imageUrl' => '/img/imagenes/novedades/cafe con pan.jpeg',
                'customLabel' => 'Especial de la manana',
                'labelExpiresAt' => '2026-12-31 23:59:00',
                'expiresAt' => '',
                'createdAt' => '2026-09-24 09:00:00',
            ],
            [
                'id' => 'cerveza-2x1',
                'title' => 'Cerveza 2x1 para compartir',
                'category' => 'Oferta',
                'summary' => 'Una promo fresca para acompanarte en la tarde.',
                'content' => 'Ven con tus amigos y disfruta una promocion pensada para acompanar tus platillos favoritos.',
                'imageUrl' => '/img/imagenes/novedades/cerveza 2x1.jpeg',
                'customLabel' => '2x1',
                'labelExpiresAt' => '2026-12-31 23:59:00',
                'expiresAt' => '',
                'createdAt' => '2026-09-23 16:00:00',
            ],
            [
                'id' => 'noche-mexicana',
                'title' => 'Noche mexicana en Las Tablas',
                'category' => 'Evento',
                'summary' => 'Sabores, ambiente y tradicion para celebrar juntos.',
                'content' => 'Preparamos una experiencia especial con el sabor de casa, bebidas y platillos para disfrutar una noche muy mexicana.',
                'imageUrl' => '/img/imagenes/novedades/noche mexicana.jpeg',
                'customLabel' => 'Evento especial',
                'labelExpiresAt' => '2026-12-31 23:59:00',
                'expiresAt' => '',
                'createdAt' => '2026-09-22 18:00:00',
            ],
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function starterMenuItems(): array
    {
        return [
            ['id' => 'huevos-rancheros', 'name' => 'Huevos rancheros', 'description' => 'Huevos banados en salsa ranchera, servidos con frijoles y tortillas calientes.', 'price' => 95, 'type' => 'morning', 'category' => 'entradas', 'imageUrl' => '/img/imagenes/menu/platillos de la manana/huevos rancheros.jpeg'],
            ['id' => 'enchiladas-rojas', 'name' => 'Enchiladas rojas', 'description' => 'Tortillas rellenas con salsa roja de la casa, crema, queso y guarnicion.', 'price' => 110, 'type' => 'morning', 'category' => 'entradas', 'imageUrl' => '/img/imagenes/menu/platillos de la manana/enchiladas rojas.jpeg'],
            ['id' => 'enchiladas-mole', 'name' => 'Enchiladas de mole', 'description' => 'Enchiladas cubiertas con mole suave, queso fresco y un toque tradicional.', 'price' => 120, 'type' => 'morning', 'category' => 'entradas', 'imageUrl' => '/img/imagenes/menu/platillos de la manana/enchiladas de mole.jpeg'],
            ['id' => 'hotcakes', 'name' => 'Hot cakes', 'description' => 'Hot cakes doraditos con mantequilla, miel y fruta de temporada.', 'price' => 90, 'type' => 'morning', 'category' => 'postres', 'imageUrl' => '/img/imagenes/menu/platillos de la manana/hotcakes.jpeg'],
            ['id' => 'picada-sirloin', 'name' => 'Picada de sirloin', 'description' => 'Picada veracruzana con carne de sirloin, salsa y queso.', 'price' => 85, 'type' => 'morning', 'category' => 'entradas', 'imageUrl' => '/img/imagenes/menu/platillos de la manana/picada de sirlon.jpeg'],
            ['id' => 'norteno', 'name' => 'Desayuno norteno', 'description' => 'Desayuno abundante con carne, huevo, frijoles y sabor de parrilla.', 'price' => 145, 'type' => 'morning', 'category' => 'entradas', 'imageUrl' => '/img/imagenes/menu/platillos de la manana/norteño.jpeg'],
            ['id' => 'rib-eye', 'name' => 'Rib eye 350 grs.', 'description' => 'Corte jugoso a la parrilla, ideal para los amantes de la carne.', 'price' => 340, 'type' => 'afternoon', 'category' => 'cortes-importados', 'imageUrl' => '/img/imagenes/menu/platillos de la tarde/rib eye.jpeg'],
            ['id' => 'tomahawk', 'name' => 'Tomahawk prime 950 grs.', 'description' => 'Corte imponente al carbon para compartir y disfrutar sin prisa.', 'price' => 730, 'type' => 'afternoon', 'category' => 'cortes-importados', 'imageUrl' => '/img/imagenes/menu/platillos de la tarde/tomahawk.jpeg'],
            ['id' => 'tampiquena', 'name' => 'Tampiquena arrachera', 'description' => 'Arrachera con enchilada roja, guacamole, frijoles refritos y platano.', 'price' => 250, 'type' => 'afternoon', 'category' => 'cortes-americanos', 'imageUrl' => '/img/imagenes/menu/platillos de la tarde/tampiqueña.jpeg'],
            ['id' => 'pulpo-brasas', 'name' => 'Pulpo a las brasas', 'description' => 'Pulpo con toque ahumado, terminado a las brasas.', 'price' => 380, 'type' => 'afternoon', 'category' => 'platillos-del-mar', 'imageUrl' => '/img/imagenes/menu/platillos de la tarde/pulpo a las brasas.jpeg'],
            ['id' => 'hamburguesa-alemana', 'name' => 'Hamburguesa alemana', 'description' => 'Carne de res con salchicha, queso americano, cebolla y papas.', 'price' => 140, 'type' => 'afternoon', 'category' => 'hamburguesas-baguette', 'imageUrl' => '/img/imagenes/menu/platillos de la tarde/hamburguesa alemana.jpeg'],
            ['id' => 'tacos-sirloin', 'name' => 'Tacos de sirloin', 'description' => 'Tacos con carne de sirloin, tortillas suaves y salsas de la casa.', 'price' => 25, 'type' => 'afternoon', 'category' => 'tacos-con-sin-queso', 'imageUrl' => '/img/imagenes/menu/platillos de la tarde/tacos de sirlon.jpeg'],
            ['id' => 'baguette', 'name' => 'Baguette de sirloin', 'description' => 'Baguette con frijoles, lechuga, queso gratinado, tomate y crema de aguacate.', 'price' => 110, 'type' => 'afternoon', 'category' => 'hamburguesas-baguette', 'imageUrl' => '/img/imagenes/menu/platillos de la tarde/baguette.jpeg'],
            ['id' => 'aperol-spritz', 'name' => 'Aperol spritz', 'description' => 'Coctel fresco y burbujeante para acompanar la tarde.', 'price' => 95, 'type' => 'afternoon', 'category' => 'cocteleria', 'imageUrl' => '/img/imagenes/menu/platillos de la tarde/bebida/aperol spritz.jpeg'],
            ['id' => 'capuchino', 'name' => 'Capuchino', 'description' => 'Cafe espumoso y cremoso, perfecto para manana o sobremesa.', 'price' => 60, 'type' => 'morning', 'category' => 'cafes', 'imageUrl' => '/img/imagenes/menu/platillos de la tarde/bebida/capuchino.jpeg'],
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function starterBlogPosts(): array
    {
        return [
            ['id' => 'favoritos-parrilla', 'title' => 'Los favoritos de la parrilla', 'summary' => 'Cortes, fuego y sabor para quienes disfrutan comer bien.', 'content' => 'En Las Tablas la parrilla es parte del ambiente. Nuestros cortes se preparan para conservar su jugosidad y ese toque ahumado que invita a compartir la mesa.', 'imageUrl' => '/img/imagenes/blog/famoso1.jpeg', 'createdAt' => '2026-09-24 12:00:00'],
            ['id' => 'desayunos-con-calma', 'title' => 'Desayunos para empezar con calma', 'summary' => 'Mananas con cafe, antojitos y platillos calientitos.', 'content' => 'Un buen dia puede empezar con huevos rancheros, enchiladas, picadas o un cafe recien servido. La idea es sencilla: comer rico desde temprano.', 'imageUrl' => '/img/imagenes/blog/famoso2.jpeg', 'createdAt' => '2026-09-23 09:00:00'],
            ['id' => 'celebrar-en-las-tablas', 'title' => 'Celebrar tambien sabe mejor', 'summary' => 'Un lugar para reunirse, festejar y disfrutar juntos.', 'content' => 'Las Tablas es un punto de encuentro para familias y amigos. Entre platillos abundantes, bebidas y buen ambiente, cada visita puede sentirse como una pequena celebracion.', 'imageUrl' => '/img/imagenes/blog/festejo1.jpeg', 'createdAt' => '2026-09-22 17:00:00'],
            ['id' => 'sabores-para-compartir', 'title' => 'Sabores pensados para compartir', 'summary' => 'Del menu a la mesa, platos que se disfrutan mejor acompanados.', 'content' => 'Hay platillos que llegan a la mesa y hacen que todos quieran probar. Esa es parte de nuestra esencia: comida servida con generosidad y mucho sabor.', 'imageUrl' => '/img/imagenes/blog/felicitacion1.jpeg', 'createdAt' => '2026-09-21 14:00:00'],
        ];
    }
}
