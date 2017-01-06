<?php

namespace AR\ArticleBundle\Controller;


use Symfony\Component\HttpFoundation\Request;
use AR\ArticleBundle\Form\ArticleType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AR\ArticleBundle\Entity\Article;

class ArticleController extends Controller
{

	/**
	* [indexAction lister les article]
	* @return [type] [flux json]
	*/
	public function indexAction()
	{
		$em = $this->getDoctrine()->getManager();
		$articleRep= $em->getRepository('ARArticleBundle:Article');
		$articles = $articleRep->findAll();

		/* @var $Article Article[] */
		$extractedArticles = [];
		foreach ($articles as $article) {
			$extractedArticles[] = [
				'Title' 		=> $article->getTitle(),
				'Leading' 		=> $article->getLeadin(),
				'Body' 			=> $article->getBody(),
				'CreatedAt' 	=> $article->getCreatedAt(),
				'Slug' 			=> $article->getSlug(),
				'CreatedBy' 	=> $article->getCreatedBy(),
			];
		}

		return new JsonResponse($extractedArticles, Response::HTTP_OK );
	}

	/**
	* [indexAction visualiser un article]
	* @return [type] [flux json]
	*/
	public function viewAction($slug)
	{

		$em = $this->getDoctrine()->getManager();
		$articleRep= $em->getRepository('ARArticleBundle:Article');
		$articles = $articleRep->findBy(
			array('slug' => $slug),
			array('createdAt' => 'desc'),        
			1,                              
			0                              
		);

		if (empty($articles)) {
			return new JsonResponse(['message' => 'Article introuvable !'], Response::HTTP_NOT_FOUND);
		}

		/* @var $Article Article[] */
		$extractedArticles = [];
		foreach ($articles as $article) {
			$extractedArticles[] = [
				'Title' 		=> $article->getTitle(),
				'Leading' 		=> $article->getLeadin(),
				'Body' 			=> $article->getBody(),
				'CreatedAt' 	=> $article->getCreatedAt(),
				'Slug' 			=> $article->getSlug(),
				'CreatedBy' 	=> $article->getCreatedBy(),
				];
		}

		return new JsonResponse($extractedArticles, Response::HTTP_OK );
	}

	/**
	* [indexAction creation  d'article]
	* @return [type] [flux json]
	*/
	public function addAction(Request $request)
	{	
		$data = json_decode($request->getContent(), true);

		$article = new Article();
		$form = $this->get('form.factory')->create(new ArticleType, $article);
		$form->handleRequest($request);

		$article->setTitle($data["title"]);
		$article->setLeadin($data["leading"]);
		$article->setBody($data["body"]);
		$article->setCreatedBy($data["createdBy"]);

		$em = $this->getDoctrine()->getManager();
		$em->persist($article);
		$em->flush();

		return new JsonResponse($data, Response::HTTP_CREATED );

	}
	/**
	* [indexAction suppression d'un article]
	* @return [type] [flux json]
	*/
	public function deleteAction($id)
	{	


	$em = $this->getDoctrine()->getManager();

	$article = $em->getRepository('ARArticleBundle:Article')->find($id);

	if (null === $article) {
		return new JsonResponse(['message' => 'Article introuvable !'], Response::HTTP_NOT_FOUND);
	}

	$em->remove($article);

	$em->flush();

	return new JsonResponse(['message' => 'Article supprimé !'], Response::HTTP_OK);

	}


}
