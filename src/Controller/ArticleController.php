<?php

namespace App\Controller;

use RedBeanPHP\R;
use RedBeanPHP\RedException\SQL;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class ArticleController extends Controller
{
    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function index()
    {
        $articles = R::findAll('article');
        self::render('article/list-articles.html.twig', [
            'articles' => $articles,
        ]);
    }

    /**
     * @return void
     * @throws SQL
     */
    public static function addArticle()
    {
        if (self::isFormSubmitted()) {
            $article = R::dispense('article');
            if (isset($_FILES["imageName"]) && $_FILES["imageName"]["error"] === 0) {
                $allowedMimeType = ["image/jpeg", "image/jpg", "image/png"];
                if (in_array($_FILES["imageName"]["type"], $allowedMimeType)) {
                    $maxSize = 8 * 1024 * 1024;
                    if ((int)$_FILES["imageName"]["size"] <= $maxSize) {
                        $tmp_name = $_FILES["imageName"]["tmp_name"];
                        $name = $_FILES["imageName"]["name"];
                        $name = self::getRandomName($name);
                        if (!is_dir('img/')) {
                            mkdir('img/', '0755');
                        }
                        if (Controller::checkImageMime($tmp_name)) {
                            if (move_uploaded_file($tmp_name, 'img/' . $name)) {
                                $article->title = filter_var($_POST['title'], FILTER_SANITIZE_STRING);
                                $article->content = filter_var($_POST['content'], FILTER_SANITIZE_STRING);
                                $article->imageName = $name;

                                R::store($article);
                                header('location: /index.php?c=home');
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * @param int $id
     * @return void
     */
    public static function delete(int $id)
    {
        $article = R::findOne('article', 'id=?', [$id]);
        if ($article) {
            R::trash($article);
            header('location: /index.php?c=home');
        }
    }
}
