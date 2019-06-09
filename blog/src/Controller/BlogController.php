<?php


namespace App\Controller;

use App\Entity\Article;
use App\Entity\Category;
use App\Form\ArticleNewType;
use App\Form\ArticleSearchType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\Mapping\OrderBy;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DomCrawler\Field\TextareaFormField;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class BlogController extends AbstractController
{
    /**
     * Show all row from article's entity
     *
     * @Route("/blog", name="blog_index")
     * @return Response A response instance
     */
    public function index(): Response
    {
        $form = $this->createForm(
            ArticleSearchType::class,
            null,
            ['method' => Request::METHOD_GET]
        );

        $articles = $this->getDoctrine()
            ->getRepository(Article::class)
            ->findAll();

        if (!$articles) {
            throw $this->createNotFoundException(
                'No article found in article\'s table.'
            );
        }

        return $this->render(
            'blog/index.html.twig', [
                'articles' => $articles,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * Getting articles from a selected Category
     *
     * @Route("/blog/category/{name}", name="show_category")
     * @param Category $category
     *
     * @return Response A response instance
     */
    public function showByCategory(Category $category) : Response
    {

//          $category = $this->getDoctrine()
//              ->getRepository(Category::class)
//              ->findOneBy(['name' => $categoryName]);

//          $articles = $this->getDoctrine()
//              ->getRepository(Article::class)
//              ->findBy(['category' => $category], ['id' => 'DESC'],3);

          $articles = $category->getArticles();

        return $this->render(
            'blog/article.html.twig',
            ['category' => $category,
              'articles' => $articles]
        );
    }

    /**
     * @Route("/blog/list", name="blog_list")
     */
    public function list()
    {
        //
    }

    /**
     * Getting a article with a formatted slug for title
     *
     * @param string $slug The slugger
     *
     * @Route("blog/show/{slug<^[a-z0-9-]+$>}",
     *     defaults={"slug" = null},
     *     name="blog_show")
     * @return Response A response instance
     */
    public function show(?string $slug) : Response
    {
        if (!$slug) {
            throw $this
                ->createNotFoundException('No slug has been sent to find an article in article\'s table.');
        }

        $slug = preg_replace(
            '/-/',
            ' ', ucwords(trim(strip_tags($slug)), "-")
        );

        $article = $this->getDoctrine()
            ->getRepository(Article::class)
            ->findOneBy(['title' => mb_strtolower($slug)]);

        if (!$article) {
            throw $this->createNotFoundException(
                'No article with '.$slug.' title, found in article\'s table.'
            );
        }

        return $this->render(
            'blog/show.html.twig',
            [
                'article' => $article,
                'slug' => $slug,
            ]
        );
    }
}
