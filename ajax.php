<?php

use Iprbooks\Rki\Sdk\Client;
use Iprbooks\Rki\Sdk\collections\BooksCollection;
use Iprbooks\Rki\Sdk\Managers\IntegrationManager;
use Iprbooks\Rki\Sdk\Models\User;

define('AJAX_SCRIPT', true);
require_once('../../config.php');
require_once($CFG->dirroot . '/blocks/rki/vendor/autoload.php');

require_login();
$action = optional_param('action', "", PARAM_TEXT);
$type = optional_param('type', "", PARAM_TEXT);
$page = optional_param('page', 0, PARAM_INT);

//book filter
$filter_book = array(
    'rki-filter-book-title' => optional_param('rki-filter-book-title', "", PARAM_TEXT),
    'rki-filter-book-pubhouse' => optional_param('rki-filter-book-pubhouse', "", PARAM_TEXT),
    'rki-filter-book-author' => optional_param('rki-filter-book-author', "", PARAM_TEXT),
    'rki-filter-book-yearleft' => optional_param('rki-filter-book-yearleft', "", PARAM_TEXT),
    'rki-filter-book-yearright' => optional_param('rki-filter-book-yearright', "", PARAM_TEXT)
);

//journal filter
$filter_journal = array(
    'rki-filter-journal-title' => optional_param('rki-filter-journal-title', "", PARAM_TEXT),
    'rki-filter-journal-pubhouse' => optional_param('rki-filter-journal-pubhouse', "", PARAM_TEXT),
);


$clientId = get_config('rki', 'user_id');
$token = get_config('rki', 'user_token');

//$clientId = 187;
//$token = '5G[Usd=6]~F!b+L<a4I)Ya9S}Pb{McGX';

$content = "";
try {
    $client = new Client($clientId, $token);
} catch (Exception $e) {
    die();
}

$integrationManager = new IntegrationManager($client);

switch ($action) {
    case 'getlist':
        switch ($type) {
            case 'book':
                $booksCollection = new BooksCollection($client);

                //set filters
                $booksCollection->setFilter(BooksCollection::TITLE, $filter_book['rki-filter-book-title']);
                $booksCollection->setFilter(BooksCollection::PUBHOUSE, $filter_book['rki-filter-book-pubhouse']);
                $booksCollection->setFilter(BooksCollection::AUTHOR, $filter_book['rki-filter-book-author']);
                $booksCollection->setFilter(BooksCollection::YEAR_LEFT, $filter_book['rki-filter-book-yearleft']);
                $booksCollection->setFilter(BooksCollection::YEAR_RIGHT, $filter_book['rki-filter-book-yearright']);

                $booksCollection->setOffset($booksCollection->getLimit() * $page);
                $booksCollection->get();

                $message = $booksCollection->getMessage();

                foreach ($booksCollection as $book) {
                    $autoLoginUrl = $integrationManager->generateAutoAuthUrl($USER->email, "", User::STUDENT, $book->getBookId());

                    $content .= "<div class=\"rki-item\" data-id=\"" . $book->getBookId() . "\">
                                    <div class=\"row\" style='padding: 10px 0'>
                                        <div id=\"rki-item-image-" . $book->getBookId() . "\" class=\"col-sm-3\">
                                            <img src=\"" . "https://ros-edu.ru/" . $book->getImage() . "\" class=\"img-responsive thumbnail\" alt=\"\">
                                            <a id=\"rki-item-url-" . $book->getBookId() . "\" href=\"" . $autoLoginUrl . "\"></a>
                                        </div>
                                        <div class=\"col-sm-8\">
                                            <div id=\"rki-item-title-" . $book->getBookId() . "\"><strong>Название:</strong> " . $book->getTitle() . " </div>
                                            <div id=\"rki-item-title_additional-" . $book->getBookId() . "\" hidden><strong>Альтернативное
                                                название:</strong> " . $book->getLongTitle() . " </div>
                                            <div id=\"rki-item-pubhouse-" . $book->getBookId() . "\"><strong>Издательство:</strong> " . $book->getPubhouses() . " </div>
                                            <div id=\"rki-item-authors-" . $book->getBookId() . "\"><strong>Авторы:</strong> " . $book->getAuthors() . " </div>
                                            <div id=\"rki-item-pubyear-" . $book->getBookId() . "\"><strong>Год издания:</strong> " . $book->getYear() . " </div>
                                            <div id=\"rki-item-isbn-" . $book->getBookId() . "\"><strong>ISBN:</strong> " . $book->getIsbn() . " </div>
                                            <div id=\"rki-item-description-" . $book->getBookId() . "\" hidden><strong>Описание:</strong> " . $book->getDescription() . " </div>
                                        </div>
                                    </div>
                                </div>";
                }

                $content .= pagination($booksCollection->getTotalCount(), $page + 1);
                break;

            case 'journal':
                /*
                $journalsCollection = new JournalsCollection($client);

                //set filters
                $journalsCollection->setFilter(JournalsCollection::TITLE, $filter_journal['rki-filter-journal-title']);
                $journalsCollection->setFilter(JournalsCollection::PUBHOUSE, $filter_journal['rki-filter-journal-pubhouse']);

                $journalsCollection->setOffset($journalsCollection->getLimit() * $page);
                $journalsCollection->get();

                $message = $journalsCollection->getMessage();

                foreach ($journalsCollection as $journal) {
                    $autoLoginUrl = $integrationManager->generateAutoAuthUrl($USER->email, "", User::STUDENT, $journal->getId());
                    $content .= "<div class=\"rki-item\" data-id=\"" . $journal->getId() . "\">
                                    <div class=\"row\" style='padding: 10px 0'>
                                        <div id=\"rki-item-image-" . $journal->getId() . "\" class=\"col-sm-3\">
                                            <img src=\"" . $journal->getImage() . "\" class=\"img-responsive thumbnail\" alt=\"\">
                                            <a id=\"rki-item-url-" . $journal->getId() . "\" href=\"" . $autoLoginUrl . "\"></a>
                                        </div>
                                        <div class=\"col-sm-8\">
                                            <div id=\"rki-item-title-" . $journal->getId() . "\"><strong>Название:</strong> " . $journal->getTitle() . "</div>
                                            <div id=\"rki-item-title_additional-" . $journal->getId() . "\" hidden></div>
                                            <div id=\"rki-item-pubhouse-" . $journal->getId() . "\"><strong>Издательство:</strong> " . $journal->getPubhouse() . "</div>
                                            <div id=\"rki-item-authors-" . $journal->getId() . "\"></div>
                                            <div id=\"rki-item-pubyear-" . $journal->getId() . "\"></div>
                                            <div id=\"rki-item-description-" . $journal->getId() . "\" hidden><strong>Описание:</strong> " . $journal->getDescription() . "</div>
                                            <div id=\"rki-item-keywords-" . $journal->getId() . "\" hidden><strong>Ключевые слова:</strong> " . $journal->getKeywords() . "</div>
                                            <div id=\"rki-item-pubtype-" . $journal->getId() . "\" hidden></div>
                                        </div>
                                    </div>
                                </div>";
                }

                $content .= pagination($journalsCollection->getTotalCount(), $page + 1);
                break;
                */
            case 'user':
                break;
        }
        break;
}

if (mb_strlen($content) < 200) {
    $content = '<div style="font-size: 150%; text-align: center;">' . $message . '</div>' . $content;
}

echo json_encode(['action' => $action, 'type' => $type, 'page' => $page, 'html' => $content]);

function pagination($count, $page)
{
    $output = '';
    $output .= "<nav aria-label=\"Страница\" class=\"pagination pagination-centered justify-content-center\"><ul class=\"mt-1 pagination \">";
    $pages = ceil($count / 10);


    if ($pages > 1) {

        if ($page > 1) {
            $output .= "<li class=\"page-item\"><a data-page=\"" . ($page - 2) . "\" class=\"page-link rki-page\" ><span>«</span></a></li>";
        }
        if (($page - 3) > 0) {
            $output .= "<li class=\"page-item \"><a data-page=\"0\" class=\"page-link rki-page\">1</a></li>";
        }
        if (($page - 3) > 1) {
            $output .= "<li class=\"page-item disabled\"><span class=\"page-link rki-page\">...</span></li>";
        }


        for ($i = ($page - 2); $i <= ($page + 2); $i++) {
            if ($i < 1) continue;
            if ($i > $pages) break;
            if ($page == $i)
                $output .= "<li class=\"page-item active\"><a data-page=\"" . ($i - 1) . "\" class=\"page-link rki-page\" >" . $i . "</a ></li > ";
            else
                $output .= "<li class=\"page-item \"><a data-page=\"" . ($i - 1) . "\" class=\"page-link rki-page\">" . $i . "</a></li>";
        }


        if (($pages - ($page + 2)) > 1) {
            $output .= "<li class=\"page-item disabled\"><span class=\"page-link rki-page\">...</span></li>";
        }
        if (($pages - ($page + 2)) > 0) {
            if ($page == $pages)
                $output .= "<li class=\"page-item active\"><a data-page=\"" . ($pages - 1) . "\" class=\"page-link rki-page\" >" . $pages . "</a ></li > ";
            else
                $output .= "<li class=\"page-item \"><a data-page=\"" . ($pages - 1) . "\" class=\"page-link rki-page\">" . $pages . "</a></li>";
        }
        if ($page < $pages) {
            $output .= "<li class=\"page-item\"><a data-page=\"" . $page . "\" class=\"page-link rki-page\"><span>»</span></a></li>";
        }

    }

    $output .= "</ul></nav>";
    return $output;
}


die();
