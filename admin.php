<?php

require("config.php");// Подключение конфигов и констант
require("HelpFunctions.php");// Подключение функции trace для проверки кода 


session_start();
$action = isset($_GET['action']) ? $_GET['action'] : "";
$username = isset($_SESSION['username']) ? $_SESSION['username'] : "";

/**
 * Если $action и $logout 
 * Если это не вход или выход и пользв-ля несуществует то 
 * вывзывается метод login 
 */
if ($action != "login" && $action != "logout" && !$username) 
{
    login();
    exit;
}

/**
 * При повторном заходе уже с сессией происходит выполнение данного switch
 * и выбирается что показать пользвателю,
 * Если или по дефолту вызывается listArticles() - Показать все статьи
 */
switch ($action) 
{
    case 'login':
        login();// Вход для админа
        break;
    case 'logout':// Удалить сессию и перезайти на этотже скрипт
        logout();
        break;
    case 'newArticle'://================= Создать новую статью =================
        newArticle();
        break;
    case 'editArticle':// Редактирование статьи 
        editArticle();
        break;
    case 'deleteArticle':// Удалить статью из БД
        deleteArticle();
        break;
    case 'listCategories'://=============== Показать все категории =================
        listCategories();
        break;
    case 'newCategory':// Создать новую категорию
        newCategory();
        break;
    case 'editCategory':// Редактировать категории
        editCategory();
        break;
    case 'deleteCategory':// Удалить категорию
        deleteCategory();
        break;
    case 'listUsers'://================= Создать новую статью =================
        listUsers();
        break;
    case 'newUser':// Создать новую статью
        newUser();
        break;
    case 'editUser':// Создать новую статью
        editUser();
        break;
    case 'deleteUser':// Создать новую статью
        deleteUser();
        break;
    case 'listSubCategories'://=============== Создать новую Подкатегорию ===============
        listSubCategories();
        break;
    case 'newSubCategory':// Создать новую статью
        newSubCategory();
        break;
    case 'editSubCategories':// Редактировать новую статью
        editSubCategories();
        break;
    case 'deleteSubCategories':// Удалить новую статью
        deleteSubCategories();
        break;
    default:
        listArticles();// Показать все статьи по дефолту
}








/**
 * Авторизация пользователя (админа) -- установка значения в сессию
 */
function login() 
{
    $results = array();
    $results['pageTitle'] = "Admin Login | Widget News";
    $users = Users::getAllUsers();


    /**  
     * Если $_POST неотправлен с формы то мы даем ему форму, 
     * Если форма отправлена то проверка, на верность пароля и логина
     * Если ошибка то выводим ошибку. Если все верно то 
     * Регистр-ем Сессию и перенаправляем его наэтуже страницу только уже с регистрацией.
     */
    if (isset($_POST['login'])) 
    {
        
        /* Проверка на Админа */
        if ($_POST['username'] == ADMIN_USERNAME && $_POST['password'] == ADMIN_PASSWORD) 
        {
          // Вход прошел успешно: создаем сессию и перенаправляем на страницу администратора
          $_SESSION['username'] = ADMIN_USERNAME;
          header( "Location: admin.php");
        } 

        /* Проверка на User */        
        foreach ($users['results'] as $user) 
        {
            if($user->active == 1)
            {
                if ($_POST['username'] == $user->login) 
                {
                    if (password_verify($_POST['password'], $user->password)) 
                    {  
                        $_SESSION['username'] = $user->login;
                        header( "Location: admin.php");
                    }
                }
            }
        } 
    } 


    // Пользователь еще не получил форму: выводим форму
    require(TEMPLATE_PATH . "/admin/loginForm.php");
}


/**
 * Выход - Удалить сессию и перенаправить на этотже скрипт
 */
function logout() 
{
    unset( $_SESSION['username'] );
    header( "Location: admin.php" );
}



/**
 *  Создание новой статьи 
 */
function newArticle() 
{
	  
    $results = array();
    $results['pageTitle'] = "New Article";
    $results['formAction'] = "newArticle";

    if ( isset( $_POST['saveChanges'] ) ) 
    {
        //  trace($results);  trace($_POST);
          
        
        // В $_POST данные о статье сохраняются корректно
        // Пользователь получает форму редактирования статьи: сохраняем новую статью
        $article = new Article();
        $article->storeFormValues( $_POST );
        // trace($article);
        // 
        // 
        //А здесь данные массива $article уже неполные(есть только Число от даты, категория и полный текст статьи)          
        $article->insert();
        header( "Location: admin.php?status=changesSaved" );

    } elseif ( isset( $_POST['cancel'] ) ) {

        // Пользователь сбросил результаты редактирования: возвращаемся к списку статей
        header( "Location: admin.php" );
    } else {

        // Пользователь еще не получил форму редактирования: выводим форму
        $results['article'] = new Article;
        $data = Category::getList();
        $results['categories'] = $data['results'];
        require( TEMPLATE_PATH . "/admin/editArticle.php" );
    }
}




/**
 * Редактирование статьи
 * 
 * @return null
 */
function editArticle() {
	  
    $results = array();
    $results['pageTitle'] = "Edit Article";
    $results['formAction'] = "editArticle";

    if (isset($_POST['saveChanges'])) {

        // Пользователь получил форму редактирования статьи: сохраняем изменения
        if ( !$article = Article::getById( (int)$_POST['articleId'] ) ) {
            header( "Location: admin.php?error=articleNotFound" );
            return;
        }

        $article->storeFormValues( $_POST );
        $article->update();
        header( "Location: admin.php?status=changesSaved" );

    } elseif ( isset( $_POST['cancel'] ) ) {

        // Пользователь отказался от результатов редактирования: возвращаемся к списку статей
        header( "Location: admin.php" );
    } else {

        // Пользвоатель еще не получил форму редактирования: выводим форму
        $results['article'] = Article::getById((int)$_GET['articleId']);
        $data = Category::getList();
        $results['categories'] = $data['results'];
        require(TEMPLATE_PATH . "/admin/editArticle.php");
    }

}




/**
 * Удаление статьи по id, если ошибка то перенаправление на этотже скрипт
 * и создать ошибку - статья не найдена.
 * Если успешно то удалить статью и перенаправить на себя же 
 * и вывести сообщение что статья удалена
 */
function deleteArticle() 
{
    if ( !$article = Article::getById( (int)$_GET['articleId'] ) ) 
    {
        header( "Location: admin.php?error=articleNotFound" );
        return;
    }

    $article->delete();
    header( "Location: admin.php?status=articleDeleted" );
}






/** 
 * Вывод всех статей в админке по дефолту
 */
function listArticles() {
    $results = array();
    
    $data = Article::getList();
    $results['articles'] = $data['results'];
    $results['totalRows'] = $data['totalRows'];
    
    // trace($results);
    
    $data = Category::getList();
    $results['categories'] = array();
    foreach ($data['results'] as $category) 
    { 
        $results['categories'][$category->id] = $category;
    }
    
    $results['pageTitle'] = "Все статьи";
     
   
    // вывод сообщения об ошибке (если есть) Статья не найдена
    if (isset($_GET['error'])) 
    { 
        if ($_GET['error'] == "articleNotFound")
        {
            $results['errorMessage'] = "Error: Article not found.";
        }
    }

    
    // вывод сообщения (если есть)
    if (isset($_GET['status'])) 
    { 
        if ($_GET['status'] == "changesSaved") {
            $results['statusMessage'] = "Your changes have been saved.";
        }
        if ($_GET['status'] == "articleDeleted")  {
            $results['statusMessage'] = "Article deleted.";
        }
    }

    
    require(TEMPLATE_PATH . "/admin/listArticles.php" );// Подключение вида, для вывода всех статей наэкран
}








/**
 * 
 */
function listCategories() {
    $results = array();
    $data = Category::getList();
    $results['categories'] = $data['results'];
    $results['totalRows'] = $data['totalRows'];
    $results['pageTitle'] = "Article Categories";

    if ( isset( $_GET['error'] ) ) {
        if ( $_GET['error'] == "categoryNotFound" ) $results['errorMessage'] = "Error: Category not found.";
        if ( $_GET['error'] == "categoryContainsArticles" ) $results['errorMessage'] = "Error: Category contains articles. Delete the articles, or assign them to another category, before deleting this category.";
    }

    if ( isset( $_GET['status'] ) ) {
        if ( $_GET['status'] == "changesSaved" ) $results['statusMessage'] = "Your changes have been saved.";
        if ( $_GET['status'] == "categoryDeleted" ) $results['statusMessage'] = "Category deleted.";
    }

    require( TEMPLATE_PATH . "/admin/listCategories.php" );
}
	  
	  
function newCategory() {

    $results = array();
    $results['pageTitle'] = "New Article Category";
    $results['formAction'] = "newCategory";

    if ( isset( $_POST['saveChanges'] ) ) {

        // User has posted the category edit form: save the new category
        $category = new Category;
        $category->storeFormValues( $_POST );
        $category->insert();
        header( "Location: admin.php?action=listCategories&status=changesSaved" );

    } elseif ( isset( $_POST['cancel'] ) ) {

        // User has cancelled their edits: return to the category list
        header( "Location: admin.php?action=listCategories" );
    } else {

        // User has not posted the category edit form yet: display the form
        $results['category'] = new Category;
        require( TEMPLATE_PATH . "/admin/editCategory.php" );
    }

}


function editCategory() {

    $results = array();
    $results['pageTitle'] = "Edit Article Category";
    $results['formAction'] = "editCategory";

    if ( isset( $_POST['saveChanges'] ) ) {

        // User has posted the category edit form: save the category changes

        if ( !$category = Category::getById( (int)$_POST['categoryId'] ) ) {
          header( "Location: admin.php?action=listCategories&error=categoryNotFound" );
          return;
        }

        $category->storeFormValues( $_POST );
        $category->update();
        header( "Location: admin.php?action=listCategories&status=changesSaved" );

    } elseif ( isset( $_POST['cancel'] ) ) {

        // User has cancelled their edits: return to the category list
        header( "Location: admin.php?action=listCategories" );
    } else {

        // User has not posted the category edit form yet: display the form
        $results['category'] = Category::getById( (int)$_GET['categoryId'] );
        require( TEMPLATE_PATH . "/admin/editCategory.php" );
    }

}


function deleteCategory() {

    if ( !$category = Category::getById( (int)$_GET['categoryId'] ) ) {
        header( "Location: admin.php?action=listCategories&error=categoryNotFound" );
        return;
    }

    $articles = Article::getList( 1000000, $category->id );

    if ( $articles['totalRows'] > 0 ) {
        header( "Location: admin.php?action=listCategories&error=categoryContainsArticles" );
        return;
    }

    $category->delete();
    header( "Location: admin.php?action=listCategories&status=categoryDeleted" );
}





/*================================================= USER ====================================================*/

/**
 * 
 */
function listUsers() 
{
    if( !($_SESSION['username'] == ADMIN_USERNAME) ){ 
       header( "Location: admin.php" );
    }

        $results = array();
        $data = Users::getAllUsers();

        $results['users'] = $data['results'];
        $results['totalRows'] = $data['totalRows'];
        $results['pageTitle'] = "Users Categories";
    

        if ( isset( $_GET['error'] ) ) 
        {
            if ( $_GET['error'] == "UserNotFound" ) 
            {
                $results['errorMessage'] = "Error: User not found.";
            }
        }


        if ( isset( $_GET['status'] ) ) {
            if ( $_GET['status'] == "changesSaved" ) $results['statusMessage'] = "Your changes have been saved.";
            if ( $_GET['status'] == "userDeleted" ) $results['statusMessage'] = "Users deleted.";
        }
       
        require( TEMPLATE_PATH . "/admin/listUsers.php" );
   
}
      





  


/**
 *  Создание нового Пользвателя 
 */
function newUser() 
{
     if( !($_SESSION['username'] == ADMIN_USERNAME) ){ 
       header( "Location: admin.php" );
    }
    
    $results = array();
    $results['pageTitle'] = "New User";
    $results['formAction'] = "newUser";

    if ( isset( $_POST['saveChanges'] ) ) 
    {
        //  trace($results);  trace($_POST);
          
        
        // В $_POST данные о статье сохраняются корректно
        // Пользователь получает форму редактирования статьи: сохраняем новую статью
        $user = new Users();
        $user->storeFormValues( $_POST );
        // trace($article);
        // 
        // 
        //А здесь данные массива $article уже неполные(есть только Число от даты, категория и полный текст статьи)          
        $user->insert();
        header( "Location: admin.php?status=changesSaved" );

    } elseif ( isset( $_POST['cancel'] ) ) {

        // Пользователь сбросил результаты редактирования: возвращаемся к списку статей
        header( "Location: admin.php" );
    } else {

        // Пользователь еще не получил форму редактирования: выводим форму
        $results['user'] = new Users;
        $data = Users::getAllUsers();
        $results['users'] = $data['results'];
        require( TEMPLATE_PATH . "/admin/editUser.php" );
    }
}







/**
 * Редактирование Пользвателя
 * 
 * @return null
 */
function editUser() {
    
     if( !($_SESSION['username'] == ADMIN_USERNAME) ){ 
       header( "Location: admin.php" );
    }
    
    $results = array();
    $results['pageTitle'] = "Edit User";
    $results['formAction'] = "editUser";

    if (isset($_POST['saveChanges'])) {

  
        // Админ получил форму редактирования Пользвателя: сохраняем изменения
        if ( !$user = Users::getById( (int)$_POST['userId'] ) ) 
        {
            header( "Location: admin.php?error=UserNotFound" );
            return;
        }
  
        $user->storeFormValues( $_POST );
        $user->update();
        

        header( "Location: admin.php?status=changesSaved" );

    } elseif ( isset( $_POST['cancel'] ) ) {

        // Пользователь отказался от результатов редактирования: возвращаемся к списку Пользвателей
        header( "Location: admin.php?action=listUsers" );
    } else {

        // Пользвоатель еще не получил форму редактирования: выводим форму
        $results['user'] = Users::getById((int)$_GET['userId']);
        $data = Users::getAllUsers();
        $results['users'] = $data['results'];

        require( TEMPLATE_PATH . "/admin/editUser.php" );
    }

}




function deleteUser() 
{
    if( !($_SESSION['username'] == ADMIN_USERNAME) ){ 
       header( "Location: admin.php" );
    }
    
    if ( !$users = Users::getById( (int)$_GET['userId'] ) ) {
        header( "Location: admin.php?action=listUsers&error=usersNotFound" );
        return;
    }

    $users->delete();
    header( "Location: admin.php?action=listUsers&status=UsersDeleted" );
}




























/*================= РАЗДЕЛ ДЛЯ ПОДКАТЕГОРИЙ ==================*/

function listSubCategories() 
{
        $results = array();
        $data = subCategories::getList();

        $results['subCategories'] = $data['results'];
        $results['totalRows'] = $data['totalRows'];
        $results['pageTitle'] = "Users Categories";
    

        if ( isset( $_GET['error'] ) ) 
        {
            if ( $_GET['error'] == "UserNotFound" ) 
            {
                $results['errorMessage'] = "Error: User not found.";
            }
        }


        if ( isset( $_GET['status'] ) ) {
            if ( $_GET['status'] == "changesSaved" ) $results['statusMessage'] = "Your changes have been saved.";
            if ( $_GET['status'] == "userDeleted" ) $results['statusMessage'] = "Users deleted.";
        }
       
        require( TEMPLATE_PATH . "/admin/listSubCategories.php" );
   
}





/**
 *  Создание нового Пользвателя 
 */
function newSubCategory() 
{
    
    $results = array();
    $results['pageTitle'] = "New SubCategory";
    $results['formAction'] = "SubCategory";

    if ( isset( $_POST['saveChanges'] ) ) 
    {
        //  trace($results);  trace($_POST);
          
        
        // В $_POST данные о статье сохраняются корректно
        // Пользователь получает форму редактирования статьи: сохраняем новую статью
        $subCategories = new SubCategories();
        $subCategories->storeFormValues( $_POST );
        // trace($article);
        // 
        // 
        //А здесь данные массива $article уже неполные(есть только Число от даты, категория и полный текст статьи)          
        $subCategories->insert();
        header( "Location: admin.php?status=changesSaved" );

    } elseif ( isset( $_POST['cancel'] ) ) {

        // Пользователь сбросил результаты редактирования: возвращаемся к списку статей
        header( "Location: admin.php" );
    } else {

        // Пользователь еще не получил форму редактирования: выводим форму
        $results['user'] = new SubCategories;
        $data = SubCategories::getList();
        $results['subCategories'] = $data['results'];
        require( TEMPLATE_PATH . "/admin/editSubCategories.php" );
    }
}









/**
 * Редактирование Пользвателя
 * 
 * @return null
 */
function editSubCategories() 
{

    $results = array();
    $results['pageTitle'] = "Edit SubCategories";
    $results['formAction'] = "editSubCategories";

    if (isset($_POST['saveChanges'])) {

        // Админ получил форму редактирования Пользвателя: сохраняем изменения
        if ( !$SubCategories = SubCategories::getById( (int)$_POST['subCategoryId'] ) ) 
        {
            header( "Location: admin.php?error=UserNotFound" );
            return;
        }
  
        $SubCategories->storeFormValues( $_POST );
        $SubCategories->update();
        

        header( "Location: admin.php?status=changesSaved" );

    } elseif ( isset( $_POST['cancel'] ) ) {

        // Пользователь отказался от результатов редактирования: возвращаемся к списку Пользвателей
        header( "Location: admin.php?action=listSubCategories" );
    } else {

        // Пользвоатель еще не получил форму редактирования: выводим форму
        $results['SubCategories'] = SubCategories::getById((int)$_GET['subCategoryId']);
        $data = SubCategories::getList();
        $results['SubCategories'] = $data['results'];

        require( TEMPLATE_PATH . "/admin/editSubCategories.php" );
    }

}





/*  */
function deleteSubCategories() 
{
    if ( !$users = Users::getById( (int)$_GET['userId'] ) ) {
        header( "Location: admin.php?action=listUsers&error=usersNotFound" );
        return;
    }

    $users->delete();
    header( "Location: admin.php?action=listUsers&status=UsersDeleted" );
}