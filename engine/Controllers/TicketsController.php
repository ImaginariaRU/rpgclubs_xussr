<?php

namespace RPGCAtlas\Controllers;

use AJUR\FluentPDO\Exception;
use AJUR\FluentPDO\Query;
use Arris\AppRouter;
use Arris\Helpers\Server;
use PDOException;
use Psr\Log\LoggerInterface;
use RPGCAtlas\App;

class TicketsController extends \RPGCAtlas\AbstractClass
{
    public function __construct($options = [], LoggerInterface $logger = null)
    {
        parent::__construct($options, $logger);
    }

    /**
     * Публичная форма подачи тикета
     *
     * @param $id
     * @return void
     */
    public function formAdd($id = 0)
    {
        $this->template->assign("id_poi", $id);
        $this->template->setTemplate("tickets/form_add.tpl");
    }

    /**
     * Коллбэк публичной формы подачи тикета
     * @return void
     */
    public function callbackAdd()
    {
        // check kCaptcha

        $query = new Query(App::$pdo, includeTableAliasColumns: false);
        $dataset = [
            'ipv4'      =>  Server::getIP(),
            'id_poi'    =>  input('id_poi'),
            'sender'    =>  input('sender'),
            'email'     =>  input('email'),
            'content'   =>  input('content')
        ];

        try {
            $query = $query
                ->insertInto($this->tables->tickets)
                ->values($dataset);

            $query->execute();

        } catch (Exception $e) {
            dd($e);
        }

        $this->template->setRedirect( AppRouter::getRouter('view.places.list') );
    }


    /**
     * Вьюшка списка тикетов
     *
     * @return void
     */
    public function viewList()
    {
        $dataset = $this->getTicketsArray();

        // тут надо как-то проапдейтить, прописав color в зависимости от статуса тикета через = match
        // лень искать кусок кода и вставлять

        $this->template->assign('dataset', $dataset);

        $this->template->setTemplate('tickets/list.tpl');
    }

    public function formView($id = 0)
    {
        $this->template->assign('item', $this->getTicketOne($id));
        $this->template->setTemplate('tickets/form_edit.tpl');
    }

    public function callbackUpdate()
    {
        $query = new Query(App::$pdo, includeTableAliasColumns: false);

        $dataset = [
            'id_poi'    =>  input('id_poi'),
            'sender'    =>  input('sender'),
            'email'     =>  input('email'),
            'content'   =>  input('content'),
            // "is_public"     =>  input('is_public') == 'Y' ? 1: 0,
        ];

        try {
            $query = $query
                ->update(
                    $this->tables->tickets,
                    $dataset,
                    primaryKey: input('id'));

            $query->execute();

        } catch (PDOException|Exception $e) {
            d($dataset);
            dd($e);
        }

        $this->template->setRedirect(AppRouter::getRouter('view.ticket.list'));
    }


    /**
     * Возвращает список тикетов
     *
     * @return array
     */
    private function getTicketsArray():array
    {
        $sth = $this->pdo->query("SELECT * FROM tickets ORDER BY id DESC");

        return $sth->fetchAll() ?? [];
    }

    /**
     * Возвращает один тикет
     *
     * @param $id
     * @return array
     */
    private function getTicketOne($id = 0):array
    {
        $sth = $this->pdo->prepare("SELECT * FROM tickets WHERE id = :id");
        $sth->execute([
            'id' => $id
        ]);

        return $sth->fetch() ?? [];
    }


}