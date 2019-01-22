<?php

App::uses('AuthComponent', 'Controller/Component');

/**
 * Users Controller
 *
 * @property User $User
 */
class UsersController extends AppController {

    public $helpers = array('Html', 'Form', 'Session');
    public $components = array('Session', 'RequestHandler');

    /**
     * index method
     *
     * @return void
     */
    public function index() {
        $this->User->recursive = 0;
        $this->set('users', $this->paginate());
    }

    /**
     * view method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function view($id = null) {
        if (!$this->User->exists($id)) {
            throw new NotFoundException(__('Invalid user'));
        }
        $options = array('conditions' => array('User.' . $this->User->primaryKey => $id));
        $this->set('user', $this->User->find('first', $options));
    }

    /**
     * add method
     *
     * @return void
     */
    public function add() {
        if ($this->request->is('post')) {
            $this->User->create();
            if ($this->User->save($this->request->data)) {
                $this->Session->setFlash(__('The user has been saved'));
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The user could not be saved. Please, try again.'));
            }
        }
        $groups = $this->User->Group->find('list');
        $this->set(compact('groups'));
    }

    /**
     * edit method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function edit($id = null) {
        if (!$this->User->exists($id)) {
            throw new NotFoundException(__('Invalid user'));
        }
        if ($this->request->is('post') || $this->request->is('put')) {
            if ($this->User->save($this->request->data)) {
                $this->Session->setFlash(__('The user has been saved'));
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The user could not be saved. Please, try again.'));
            }
        } else {
            $options = array('conditions' => array('User.' . $this->User->primaryKey => $id));
            $this->request->data = $this->User->find('first', $options);
        }
        $groups = $this->User->Group->find('list');
        $this->set(compact('groups'));
    }

    /**
     * delete method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function delete($id = null) {
        $this->User->id = $id;
        if (!$this->User->exists()) {
            throw new NotFoundException(__('Invalid user'));
        }
        $this->request->onlyAllow('post', 'delete');
        if ($this->User->delete()) {
            $this->Session->setFlash(__('User deleted'));
            $this->redirect(array('action' => 'index'));
        }
        $this->Session->setFlash(__('User was not deleted'));
        $this->redirect(array('action' => 'index'));
    }

    public function login() {
        $_ORI_LOGIN = 1;
        $_LOGINING = 2;
        $_ERR_LOGIN = 3;

        $login_flag = $_ORI_LOGIN;
        $errMsg = "";

        $is_ajax = $this->request->is("ajax");
        if ($this->Session->read('Auth.User')) {
            $login_flag = $_LOGINING;
        } elseif (isset($this->request->data['rt']) || isset($this->request->query['ssid'])) {
            $userData = isset($this->request->data['rt']) ? $this->remoteLogin() :  $this->remoteValidate(); 
            if ($userData && $this->Auth->login($this->reviseRemoteUserData($userData))) {
                $login_flag = $_LOGINING;
            } else {
                $login_flag = $_ERR_LOGIN;
                $errMsg = "用户名或密码错误!";
            }
        }
        $this->Session->delete('Message');
        if ($is_ajax) {
            $err = $login_flag == $_LOGINING ? 0 : 1;
            echo json_encode(array("err" => $err, "msg" => $errMsg));
            exit();
        }
        if ($login_flag == $_LOGINING) {
            $this->redirect($this->Auth->redirect());
        } elseif ($login_flag == $_ERR_LOGIN) {
            $this->Session->setFlash(__($errMsg), 'default', array(), 'auth');
        }
    }

    public function logout() {

        $this->Cookie->path = '/';
        //$this->Cookie->domain = 'max.com';
        $this->Cookie->delete('ssid');
        $this->Cookie->delete('m');
        $this->Cookie->delete('s_t');
        $this->Cookie->destroy();

        $this->Session->setFlash('');
        $redirect = $this->Auth->logout();

        $this->redirect($redirect);
    }

    public function initDB() {
        $group = $this->User->Group;
        //Allow admins to everything
        $group->id = 1;
        $this->Acl->allow($group, 'controllers');

        //allow managers to posts and widgets
        $group->id = 2;
        $this->Acl->deny($group, 'controllers');
        $this->Acl->allow($group, 'controllers/Posts');
        $this->Acl->allow($group, 'controllers/Widgets');
        $this->Acl->allow($group, 'controllers/Images');
        $this->Acl->allow($group, 'controllers/Speedfolds');
        $this->Acl->allow($group, 'controllers/Uploadlers');

        //allow users to only add and edit on posts and widgets
        $group->id = 3;
        $this->Acl->deny($group, 'controllers');
        $this->Acl->allow($group, 'controllers/Images');
        $this->Acl->allow($group, 'controllers/Speedfolds');
        $this->Acl->allow($group, 'controllers/Uploadlers');
        $this->Acl->allow($group, 'controllers/Posts/add');
        $this->Acl->allow($group, 'controllers/Posts/edit');
        $this->Acl->allow($group, 'controllers/Widgets/add');
        $this->Acl->allow($group, 'controllers/Widgets/edit');
        //we add an exit to avoid an ugly "missing views" error message
        echo "all done";
        exit;
    }

    protected function remoteValidate() {
        $json_url = Configure::read("remote_login_url") . '/login?a=remoteValidate';

        $ssid = isset($this->request->query['ssid']) ? $this->request->query['ssid'] : ($this->Cookie->read('ssid') ? $this->Cookie->read('ssid') : '');
        $user = isset($this->request->query['m']) ? $this->request->query['m'] : ($this->Cookie->read('m') ? $this->Cookie->read('m') : '');
        $token = isset($this->request->query['s_t']) ? $this->request->query['s_t'] : ($this->Cookie->read('s_t') ? $this->Cookie->read('s_t') : '');

        if ($ssid == '' || $user == '' || $token == '')
            return false;
        $data = array('ssid' => $ssid, 'user' => $user, 'token' => $token); 

        $json_string = json_encode($data);
        $ch = curl_init($json_url);
        $options = array(
            CURLOPT_POST => 1,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => array('Content-type: application/json'),
            CURLOPT_POSTFIELDS => $json_string
        );

        curl_setopt_array($ch, $options); 

        $result = json_decode(curl_exec($ch), true);
        curl_close($ch);
        if ($result['err'] == 0) {
            $this->Cookie->path = '/';            
            $this->Cookie->write('ssid', $result['maker']['ssid'], false, 3600);
            $this->Cookie->write('m', $result['maker']['account'], false, 3600);
            $this->Cookie->write('s_t', $result['maker']['token'], false, 3600);
            return $result['maker'];
        } else {
            $this->Session->setFlash($result['msg']);
            return false;
        }
    }

    protected function remoteLogin() {
        $json_url = Configure::read("remote_login_url") . '/login?a=remoteLogin';
        $username = $this->request->data['login_account'];
        $password = $this->request->data['login_password'];
        //$json_string = json_encode(array("sdf"));
        $ch = curl_init($json_url);
        $options = array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_USERPWD => $username . ":" . $password,
                //  CURLOPT_HTTPHEADER => array('Content-type: application/json'),
                // CURLOPT_POSTFIELDS => $json_string
        );


        curl_setopt_array($ch, $options);
        $result = json_decode(curl_exec($ch), true);
        curl_close($ch);
        if ($result['err'] == 0) {
            $this->Cookie->path = '/';
            // $this->Cookie->domain = 'max.com';
            $this->Cookie->write('ssid', $result['maker']['ssid'], false, 3600);
            $this->Cookie->write('m', $result['maker']['account'], false, 3600);
            $this->Cookie->write('s_t', $result['maker']['token'], false, 3600);
            return $result['maker'];
        } else {
            $this->Session->setFlash($result['msg']);
            return false;
        }
    }

    protected function reviseRemoteUserData($userData) {
        $user = $userData;
        $group_id = 3;
        $group = $this->User->Group->findById($group_id);
        $user['group_id'] = $group_id;
        $user['Group'] = $group['Group'];

        return $user;
    }

    public function remoteLogout() {
        $json_url = Configure::read("remote_login_url") . '/logout?a=remoteLogout';

        $ssid = $this->Session->read('Auth.User.ssid') ? $this->Session->read('Auth.User.ssid') : '';
        $user_id = $this->Session->read('Auth.User.m_maker_id') ? $this->Session->read('Auth.User.m_maker_id') : '';
        $token = $this->Session->read('Auth.User.token') ? $this->Session->read('Auth.User.token') : '';

        $data = array('ssid' => $ssid, 'user_id' => $user_id, 'token' => $token);

        $json_string = json_encode($data);
        $ch = curl_init($json_url);
        $options = array(
            CURLOPT_POST => 1,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => array('Content-type: application/json'),
            CURLOPT_POSTFIELDS => $json_string
        );

        curl_setopt_array($ch, $options);
        $result = curl_exec($ch);
        curl_close($ch);
        return json_decode($result, true);
    }

    public function pageCache() {
        $data = array("pc_login" => Configure::read("remote_login_url"), "pc_host" => Configure::read("site_url"));
        $this->set('data', $data);
        $this->set('_serialize', array('data'));
    }

    public function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow('pageCache');
    }

}
