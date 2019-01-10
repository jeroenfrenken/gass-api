<?php
/**
 * Created by PhpStorm.
 * User: jeroenfrenken
 * Date: 09/01/2019
 * Time: 19:24
 */
namespace App\Controller\Services;

use App\Entity\ActionRegister;
use Doctrine\ORM\EntityManagerInterface;

class ActionRegisterService
{

    /*
     * CONST ACTION_*  the different action types
     * CONST *_COOL_DOWN_TIME the time that needs to be passed before a action is possible again
     * CONST *_MAX_TIMES the max time a event can occur in a cool down period
     */

    public const ACTION_LOGIN = 'action_login';
    public const ACTION_REGISTER = 'action_register';
    public const ACTION_OTHER = 'action_other';

    public const LOGIN_COOL_DOWN_TIME = 3600;
    public const LOGIN_MAX_TIMES = 2;

    public const REGISTER_COOL_DOWN_TIME = 3600;
    public const REGISTER_MAX_TIMES = 1;

    public const OTHER_COOL_DOWN_TIME = 10;
    public const OTHER_MAX_TIMES = 5;

    private $_doctrine;

    /**
     * Gets the EntityManagerInterface to access doctrine
     *
     * ActionRegisterService constructor.
     * @param EntityManagerInterface $doctrine
     */
    public function __construct(EntityManagerInterface $doctrine)
    {
        $this->_doctrine = $doctrine;
    }

    /**
     * Registers a new action in the database
     *
     * @param string $ip
     * @param string $action
     * @return bool
     */
    public function registerAction(string $ip, string $action) {

        $actionRegister = new ActionRegister();

        $actionRegister->setAction($action);
        $actionRegister->setDate(new \DateTime('now'));
        $actionRegister->setIdentifier($this->_hashIp($ip));

        $em = $this->_doctrine;

        $em->persist($actionRegister);

        try {
            $em->flush();
            return true;
        } catch (\Exception $e) {
            return false;
        }

    }

    /**
     * Checks if a action is possible based on past action by the users identifier
     *
     * @param string $ip
     * @param string $action
     * @return bool
     */
    public function canDoAction(string $ip, string $action) {

        return $this->_isActionAllowed($this->_hashIp($ip), $action);

    }

    /**
     * Converts a ip to a identifier.
     * Identifiers are stored in the database and IP's not this is done because of user privacy
     *
     * @param string $ip
     * @return string
     */
    private function _hashIp(string $ip) {

        //A salt is used to make the change of reverse hashing way smaller
        $salt = 'uxCB2zr|WPj2MFD[Wh*';

        return hash('sha512', $ip . $salt);

    }

    /**
     * Checks if a action is allowed based on:
     * - identifier
     * - action type
     * - timestamp - cooldowntime
     * - max allowed actions in a cooldown periode
     *
     * @param string $identifier
     * @param string $action
     * @return bool
     */
    private function _isActionAllowed(string $identifier, string $action) {

        $time = new \DateTime('now');
        $timestamp = $time->getTimestamp();

        $count = 5;

        switch ($action) {
            case self::ACTION_LOGIN:
                $time->setTimestamp($timestamp - self::LOGIN_COOL_DOWN_TIME);
                $count = self::LOGIN_MAX_TIMES;
                break;
            case self::ACTION_REGISTER:
                $time->setTimestamp($timestamp - self::REGISTER_COOL_DOWN_TIME);
                $count = self::REGISTER_MAX_TIMES;
                break;
            case self::ACTION_OTHER:
                $time->setTimestamp($timestamp - self::OTHER_COOL_DOWN_TIME);
                $count = self::OTHER_MAX_TIMES;
                break;
        }

        $pastActions = $this->
            _doctrine->
            getRepository(ActionRegister::class)->
            findByIdentifierAndHigherTimeAndAction($identifier, $time, $action);

        return count($pastActions) < $count;

    }

}