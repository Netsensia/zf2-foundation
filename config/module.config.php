<?php

return array(
    'controllers' => array(
        'invokables' => array(
            'Netsensia\Controller\Register' => 'Netsensia\Controller\RegisterController',
            'Netsensia\Controller\Auth' => 'Netsensia\Controller\AuthController',
            'Netsensia\Controller\Locale' => 'Netsensia\Controller\LocaleController',
            'Netsensia\Controller\Help' => 'Netsensia\Controller\HelpController',
        ),
    ),
    'controller_plugins' => array(
        'invokables' => array(
            'processForm' => 'Netsensia\Controller\Plugin\ProcessForm',
        )
    ),    
    'service_manager' => array(
        'invokables' => array(
            'AuthAdaptorMySQL' => 'Netsensia\Adaptor\Auth\AuthAdaptorMySQL',
            'AuthenticationService' => 'Zend\Authentication\AuthenticationService',
         ),
     ),
    'router' => array(
        'routes' => array(
            'login' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/login',
                    'defaults' => array(
                        'controller' => 'Netsensia\Controller\Auth',
                        'action'     => 'login',
                    ),
                ),
            ),
            
            'logout' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/logout',
                    'defaults' => array(
                        'controller' => 'Netsensia\Controller\Auth',
                        'action'     => 'logout',
                    ),
                ),
            ),

            'help' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/help',
                    'defaults' => array(
                        'controller' => 'Netsensia\Controller\Help',
                        'action'     => 'index',
                    ),
                ),
            ),
            
            'contact' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/contact',
                    'defaults' => array(
                        'controller' => 'Netsensia\Controller\Help',
                        'action'     => 'contact',
                    ),
                ),
            ),
            
            'register' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/register',
                    'defaults' => array(
                        'controller' => 'Netsensia\Controller\Register',
                        'action'     => 'index',
                    ),
                ),
            ),
            
            'password-reset' => array(
                'type' => 'Literal',
                'options' => array(
                    'route'    => '/password-reset',
                    'defaults' => array(
                        'controller' => 'Netsensia\Controller\Register',
                        'action'     => 'passwordReset',
                    ),
                ),
            ),

            'update-password' => array(
                'type' => 'Segment',
                'options' => array(
                    'route'    => '/update-password[/:password-reset-code]',
                    'defaults' => array(
                        'controller' => 'Netsensia\Controller\Register',
                        'action'     => 'updatePassword',
                    ),
                ),
            ),

            'new-user' => array(
                'type'    => 'Literal',
                'options' => array(
                    'route'    => '/welcome',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Netsensia\Controller',
                        'controller'    => 'Register',
                        'action'        => 'newUser',
                    ),
                ),
                'may_terminate' => true,
            ),
            
            'validate-email' => array(
                'type'    => 'Segment',
                'options' => array(
                    'route'    => '/validate-email/[:code]',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Netsensia\Controller',
                        'controller'    => 'Register',
                        'action'        => 'validateEmail',
                    ),
                ),
                'may_terminate' => true,
            ),
            
            'locale' => array(
                'type' => 'Segment',
                'options' => array(
                    'route'    => '/locale/[:locale]',
                    'defaults' => array(
                        'controller' => 'Netsensia\Controller\Locale',
                        'action'     => 'set',
                    ),
                ),
            ),            
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'Netsensia' => __DIR__ . '/../view',
        ),
    ),
);
