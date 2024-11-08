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
    public function formAdd(mixed $id = 0): void
    {
        if ($session = App::$flash->getMessage('json_session')) {
            $this->template->assign("session", $session[0]);
            App::$flash->clearMessage('json_session');
        }

        $this->template->assign("id_poi", $id);
        $this->template->setTemplate("tickets/form_add_ticket.tpl");
    }

    /**
     * Коллбэк публичной формы подачи тикета
     * @return void
     */
    public function callbackAdd(): void
    {
        if (!App::$auth->isLoggedIn()) {
            if ($_REQUEST['captcha'] != $_SESSION['captcha_keystring']) {
                unset($_REQUEST['captcha']); // иначе значение капчи окажется сохранено в flash-message
                App::$flash->addMessage('error', 'Капча введена неправильно!');
                App::$flash->addMessage('json_session', json_encode($_REQUEST));
                $this->template->setRedirect(AppRouter::getRouter('form.add.ticket', [ 'id' => $_REQUEST['id_poi'] ]));
                return;
            }
        }

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

        $this->template->setRedirect( AppRouter::getRouter('view.poi.list') );
    }


    /**
     * Вьюшка списка тикетов
     *
     * @return void
     */
    public function viewList(): void
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
        $this->template->setTemplate('tickets/form_edit_ticket.tpl');
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
     * @param mixed $id
     * @return array
     */
    private function getTicketOne(mixed $id = 0):array
    {
        $sth = $this->pdo->prepare("SELECT * FROM tickets WHERE id = :id");
        $sth->execute([
            'id' => (int)$id
        ]);

        return $sth->fetch() ?? [];
    }


}