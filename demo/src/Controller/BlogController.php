<?php
namespace App\Controller;

require '../vendor/autoload.php';

use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use Doctrine\Persistence\ObjectManager;
//use Doctrine\Common\Persistence\ObjectManager;
//use Doctrine\Common\Persistence\ClassLoader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

//:'@doctrine.orm.default_entity_manager';
//use Doctrine\ORM\EntityManager;
//use Doctrine\ORM\EntityManager;

class BlogController extends AbstractController
{
	/**
	 * @Route("/blog", name="blog")
	 */
	public function index(ArticleRepository $repo)
	{

		$repo     = $this->getDoctrine()->getRepository(Article::class);
		$articles = $repo->findAll();

		return $this->render('blog/index.html.twig', [
			'controller_name' => 'BlogController',
			'articles'        => $articles,
		]);
	}

	/**
	 * @Route("/",name="home")
	 */

	public function home()
	{
		return $this->render('blog/home.html.twig', [
			'title' => 'Bienvenue ici les amis!',
			'age'   => 31,
		]);
	}

	/**
	 * @Route("/blog/new", name="blog_create")
	 * @Route("/blog/{id}/edit/", name ="blog_edit")
	 */
	public function form(Article $article = null, Request $request, ObjectManager $manager)
	{
		if (!$article) {
            $article = new Article();
		}
		//$form = $this->createFormBuilder($article)
		// 	->add('Title')
		// 	->add('content')
		// 	->add('image', )
		// 	->getForm();
        // $form->handleRequest($request);
		// dump($article);

        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			if (!$article->getId()) {
				$article->setCreatedAt(new \DateTime());
			}

			$manager->persist($article);
			$manager->flush();
			return $this->redirectToRoute('blog_show', ['id' => $article->getId()]);
		}

		return $this->render('blog/create.html.twig', [
			'formArticle' => $form->createView(),
			'editMode'    => $article->getId() !== null,
		]);

	}

	/**
	 * @Route("/blog/{id}", name="blog_show")
	 */
	public function show(Article $article)
	{
		/*$repo = $this->getDoctrine()->getRepository(Article::class);
		$article=$repo->find($id);*/

		return $this->render('blog/show.html.twig', [
			'article' => $article,
		]);
	}

}
