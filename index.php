<?php
//phpinfo(); die();
/*


CREATE TABLE subCategories (
  id INT(11) NOT NULL AUTO_INCREMENT,
  categoryID SMALLINT(5) UNSIGNED NOT NULL,
  name VARCHAR(255) NOT NULL,
  description VARCHAR(1000) NOT NULL,
  PRIMARY KEY (id),
  FOREIGN KEY (categoryID) REFERENCES categories (id) ON UPDATE CASCADE ON DELETE CASCADE
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB
AUTO_INCREMENT=14
;


INSERT INTO `subCategories`(`id`, `categoryID`, `name`, `description`) VALUES (null,1,'Первая подкатегория','Описание')


Select t1.`id`, t2.`name` as parentCategory, t1.`name`, t1.`description`
from `subCategories` t1, `categories` t2
where t1.categoryID = t2.id
========================================================================
Внести изменение в таблицу articles
ALTER TABLE `articles`
	ADD COLUMN `active` SMALLINT(5) NOT NULL DEFAULT '1' AFTER `content`;

Преведущая версия запроса на выборку
FROM articles $categoryClause 



CREATE TABLE `users` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `login` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `active` smallint(5) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;


INSERT INTO `users`(`id`, `login`, `password`, `active`) VALUES (1, 'sudo', '$2y$10$2oOZ7T31EUqhrrwxl9OA6OvcwzDLPnozpJj1NFmePIvM0J3X2DS4a', 0)


*/
require("config.php");
require("HelpFunctions.php");

try {
    initApplication();
} catch (Exception $e) { 
    $results['errorMessage'] = $e->getMessage();
    require(TEMPLATE_PATH . "/viewErrorPage.php");
}


function initApplication()
{
    $action = isset($_GET['action']) ? $_GET['action'] : "";

    switch ($action) 
    {
        case 'archive':
          archive();
          break;
        case 'viewArticle':
          viewArticle();
          break;
        default:
          homepage();
    }

    getCanvas();
}

function getCanvas()
{
    echo "<canvas id='c'></canvas>";
    echo "<span id = 'author'>Popckov</span>";
}

function archive() 
{
    $results = [];
    
    $categoryId = ( isset( $_GET['categoryId'] ) && $_GET['categoryId'] ) ? (int)$_GET['categoryId'] : null;
    
    $results['category'] = Category::getById( $categoryId );
    
    /*====== ВОТ ТУТ Я ИЗМЕНИЛ( ДЛЯ ОТОБРАЖЕНИЯ ТОЛЬКО АКТИВНЫХ СТАТЕЙ ) ======*/
    $data = Article::getList( 100000, $results['category'] ? $results['category']->id : null, "publicationDate DESC" ,1 );
    
    $results['articles'] = $data['results'];
    $results['totalRows'] = $data['totalRows'];
    
    $data = Category::getList();
    $results['categories'] = array();
    
    foreach ( $data['results'] as $category ) {
        $results['categories'][$category->id] = $category;
    }
    
    $results['pageHeading'] = $results['category'] ?  $results['category']->name : "Article Archive";
    $results['pageTitle'] = $results['pageHeading'] . " | Widget News";
    
    require( TEMPLATE_PATH . "/archive.php" );
}

/**
 * Загрузка страницы с конкретной статьёй
 * 
 * @return null
 */
function viewArticle() 
{   
    if ( !isset($_GET["articleId"]) || !$_GET["articleId"] ) {
      homepage();
      return;
    }

    $results = array();
    $articleId = (int)$_GET["articleId"];
    $results['article'] = Article::getById($articleId);
    
    if (!$results['article']) {
        throw new Exception("Статья с id = $articleId не найдена");
    }
    
    $results['category'] = Category::getById($results['article']->categoryId);
    $results['pageTitle'] = $results['article']->title . " | Простая CMS";
    
    require(TEMPLATE_PATH . "/viewArticle.php");
}

/**
 * Вывод домашней ("главной") страницы сайта
 */
function homepage() 
{
    $results = array();
    $data = Article::getList(HOMEPAGE_NUM_ARTICLES, null, "publicationDate DESC", 1);
    $results['articles'] = $data['results'];
    $results['totalRows'] = $data['totalRows'];
    
    $data = Category::getList();
    $results['categories'] = array();
    foreach ( $data['results'] as $category ) { 
        $results['categories'][$category->id] = $category;
    } 
    
    $results['pageTitle'] = "Простая CMS на PHP";

    // trace($data);
    
    require(TEMPLATE_PATH . "/homepage.php");
    
}