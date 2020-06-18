<?php
/**
 * Created by IntelliJ IDEA.
 * User: BigBoss
 * Date: 17/05/2020
 * Time: 17:00
 */

namespace App\Service;


use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Environment;

class Pagination
{
	private $entityClass;
	private $limit;
	private $offset;
	private $currentPage = 1;
	private $route;
	/**
	 * @var Environment
	 */
	private $twig;
	private $criteria = [];
	private $em;
	private $entityTemplatePath;
	private $buttonTemplatePath;
	private $routeParameters = [];

	public function __construct(EntityManagerInterface $em, Environment $twig, RequestStack $requestStack, String $buttonTemplatePath, int $limit)
	{
		$request = $requestStack->getCurrentRequest();
		$this->route        = $request->attributes->get('_route');
		if($request->get('page')){
			$this->currentPage  = $request->get('page');
		}
		if($request->get('offset')){
			$this->offset  = $request->get('offset');
		}
		$this->em           = $em;
		$this->twig         = $twig;
		$this->buttonTemplatePath = $buttonTemplatePath;
		$this->limit = $limit;
	}

	/**
	 * @throws \Twig\Error\LoaderError
	 * @throws \Twig\Error\RuntimeError
	 * @throws \Twig\Error\SyntaxError
	 * @throws \Exception
	 */
	public function renderView(){

		return $this->twig->render($this->entityTemplatePath,[
				$this->getDataName() => $this->getData(),
			]
		);
	}

	/**
	 * Display button load more
	 *
	 * @param int|null $startPage
	 *
	 * @throws \Twig\Error\LoaderError
	 * @throws \Twig\Error\RuntimeError
	 * @throws \Twig\Error\SyntaxError
	 */
	public function display(int $startPage = null){
		if (!$startPage){
			$startPage = $this->currentPage;
		}

		$this->twig->display($this->buttonTemplatePath, [
			'pages' => $this->getPages(),
			'route' => $this->route,
			'page'  => $startPage,
			'parameters' => $this->routeParameters
		]);
	}

	/**
	 * @return array[]
	 * @throws \Exception
	 */
	public function getData(){
		$this->error();
		//determine offset if object isn't deleted
		if($this->offset == null){
			$this->offset = $this->currentPage * $this->limit - $this->limit;
		}
		$repository = $this->em->getRepository($this->entityClass);

		return $repository->findBy($this->criteria, ['createdAt' => 'DESC'], $this->limit, $this->offset);
	}

	/**
	 * @return integer
	 * @throws \Exception
	 */
	public function getPages(){
		$this->error();

		$total = count($this->em->getRepository($this->entityClass)->findBy($this->criteria));

		return ceil($total / $this->limit);
	}

	/**
	 * @return String
	 */
	public function getEntityClass()
	{
		return $this->entityClass;
	}

	/**
	 * @param String $entityClass
	 *
	 * @return Pagination
	 */
	public function setEntityClass(String $entityClass): self
	{
		$this->entityClass = $entityClass;

		return $this;
	}

	/**
	 * @return int
	 */
	public function getLimit(): int
	{
		return $this->limit;
	}

	/**
	 * @param int $limit
	 *
	 * @return Pagination
	 */
	public function setLimit(int $limit): self
	{
		$this->limit = $limit;

		return $this;
	}

	/**
	 * @return int
	 */
	public function getOffset()
	{
		return $this->offset;
	}

	/**
	 * @param mixed $offset
	 *
	 * @return self
	 */
	public function setOffset($offset): self
	{
		$this->offset = $offset;

		return $this;
	}

	/**
	 * @return int
	 */
	public function getCurrentPage(): int
	{
		return $this->currentPage;
	}

	/**
	 * @param int $currentPage
	 *
	 * @return Pagination
	 */
	public function setCurrentPage(int $currentPage): self
	{
		$this->currentPage = $currentPage;

		return $this;
	}

	/**
	 * @return String
	 */
	public function getRoute()
	{
		return $this->route;
	}

	/**
	 * @param String $route
	 *
	 * @return Pagination
	 */
	public function setRoute(String $route): self
	{
		$this->route = $route;

		return $this;
	}

	/**
	 * @return String
	 */
	public function getEntityTemplatePath()
	{
		return $this->entityTemplatePath;
	}

	/**
	 * @param String $entityTemplatePath
	 *
	 * @return Pagination
	 */
	public function setEntityTemplatePath(String $entityTemplatePath): self
	{
		$this->entityTemplatePath = $entityTemplatePath;

		return $this;
	}

	/**
	 * @param String $buttonTemplatePath
	 *
	 * @return Pagination
	 */
	public function setButtonTemplatePath(String $buttonTemplatePath): self
	{
		$this->buttonTemplatePath = $buttonTemplatePath;

		return $this;
	}

	/**
	 * @return array
	 */
	public function getCriteria(): array
	{
		return $this->criteria;
	}

	/**
	 * @param array $criteria
	 *
	 * @return Pagination
	 */
	public function setCriteria(array $criteria): Pagination
	{
		$this->criteria = $criteria;
		return $this;
	}

	/**
	 * @param mixed $routeParameters
	 *
	 * @return Pagination
	 */
	public function setRouteParameters(array $routeParameters)
	{
		$this->routeParameters = $routeParameters;
		return $this;
	}

	/**
	 * Get name of data from class name
	 *
	 * @return String
	 */
	private function getDataName(): String{

		$name = explode('\\', $this->getEntityClass());
		return strtolower(end($name))."s" ;
	}

	/**
	 * @throws \Exception
	 */
	private function error(){

		if(empty($this->entityClass)){
			throw  new \Exception("The entity used by pagination service is not specified ! Resolve the problem by using the method [ setEntityClass() ] from object 'Pagination'");
		}
	}
}