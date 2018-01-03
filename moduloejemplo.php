<?php

if (!defined('_PS_VERSION_')) exit;


/**
 * Modulo ejemplo para mostrar informaciÃ³n de
 * pesetacoin en prestashop
 *
 * @package    moduloejemplo
 * @subpackage
 * @author     marcos.trfn@gmail.com
 * @version    1.0
 *
 */
 
require('model/pesetacoin.php');

class Moduloejemplo extends Module

{
	/*
	* Constructor de la clase
	*
	* @param 
	* @return
	*/
	public function __construct()
	{
		$this->name = 'moduloejemplo';
		$this->tab = 'front_office_features';
		$this->version = '1.0.0';
		$this->author = 'pesetacoin';
		$this->need_instance = 0;
		$this->ps_versions_compliancy = array(
			'min' => '1.6',
			'max' => _PS_VERSION_
		);
		$this->bootstrap = true;
		parent::__construct();
		
		$this->displayName = $this->l('modulo ejemplo');
		$this->description = $this->l('Descripcion del modulo.');
		$this->confirmUninstall = $this->l('¿Desea desinstalar?');
		
		$this->warning = $this->l('advertencia');
		
		/* valores de configuracion */
		if (!Configuration::get('MYMODULE_NAME')) {
			Configuration::updateValue('MYMODULE_NAME', 'pesetacoin_ini');
		}
	
		
		
	}

	/*
	* Instalación del módulo. Aqui registramos las posiciones
	* donde mostraremos el módulo y definimos las variables
	* que queremos manejar.
	*
	* @param 
	* @return 
	*/
	public function install()
	{		
		/* 
		* Si la funcion multistore esta habilitada, 
		* activamos el modulo para todas las
		* tiendas de la instalacion
		*/
		if (Shop::isFeatureActive()) 
		{
			Shop::setContext(Shop::CONTEXT_ALL);
		}
		
		/* posiciones de los modulos y header para añadir css */
		$hookName = 'displayTop';
		$hookName2 = 'displayFooter';
		return parent::install() && $this->registerHook('header') && $this->registerHook($hookName) && $this->registerHook($hookName2);
	}

	/*
	
	* Desinstalar el módulo
	*
	* @param 
	* @return 
	*/
	public function uninstall()
	{
		$hookName = 'displayTop';
		$hookName2 = 'displayFooter';
		return parent::uninstall() && $this->registerHook('header') && $this->registerHook($hookName) && $this->registerHook($hookName2) && Configuration::deleteByName('MYMODULE_NAME');
	}

	/*
	*  Hook para visualizar en posicion Top
	*
	* @param 
	* @return 
	*/
	public function hookDisplayTop()
	{
		$this->context->smarty->assign(array(
			'my_module_name' => Configuration::get('MYMODULE_NAME') ,
			'image_baseurl' => $this->_path . 'views/img/'
		));
		return $this->display(__FILE__, 'views/templates/hook/moduloejemplo2.tpl');
	}

	/*
	* Hook para visualizar en posicion Footer
	*
	* @param 
	* @return 
	*/
	public function hookDisplayFooter()
	{
		
		$obj_pesetacoin = new PesetaCoinFunciones();
		$getPriceEur = $obj_pesetacoin->getPriceEur();
		$getPriceUsd = $obj_pesetacoin->getPriceUsd();
		$getPriceBtc = $obj_pesetacoin->getPriceBtc();
		
		$this->context->smarty->assign(array(
			'my_module_name' => Configuration::get('MYMODULE_NAME') ,
			'image_baseurl' => $this->_path . 'views/img/',
			'getPriceEur' => $getPriceEur,
			'getPriceUsd' => $getPriceUsd,
			'getPriceBtc' => $getPriceBtc
		));
		return $this->display(__FILE__, 'views/templates/hook/display-footer.tpl');
	}

	/*
	* Añade Css
	*
	* @param 
	* @return 
	*/
	public function hookDisplayHeader()
	{
		$this->context->controller->addCSS($this->_path . 'views/css/moduloejemplo.css', 'all');
	}
	
	
	
	
	
	
	
	
	
	
	/*
	* Configuracion del modulo
	*
	* @param 
	* @return 
	*/
	
	public function getContent()
	{
		$output = null;
	 
		if (Tools::isSubmit('submit'.$this->name))
		{
			$my_module_name = strval(Tools::getValue('MYMODULE_NAME'));
			if (!$my_module_name
			  || empty($my_module_name)
			  || !Validate::isGenericName($my_module_name))
				$output .= $this->displayError($this->l('Invalid Configuration value'));
			else
			{
				Configuration::updateValue('MYMODULE_NAME', $my_module_name);
				$output .= $this->displayConfirmation($this->l('Settings updated'));
			}
		}
		return $output.$this->displayForm();
	}


	/*
	* Muestra el formulario de configuracion
	*
	* @param 
	* @return 
	*/
	public function displayForm()
	{
		// Get default language
		$default_lang = (int)Configuration::get('PS_LANG_DEFAULT');
		 
		// Init Fields form array
		$fields_form[0]['form'] = array(
			'legend' => array(
				'title' => $this->l('Settings'),
			),
			'input' => array(
				array(
					'type' => 'text',
					'label' => $this->l('Configuration value'),
					'name' => 'MYMODULE_NAME',
					'size' => 20,
					'required' => true
				)
			),
			'submit' => array(
				'title' => $this->l('Save'),
				'class' => 'btn btn-default pull-right'
			)
		);
		 
		$helper = new HelperForm();
		 
		// Module, token and currentIndex
		$helper->module = $this;
		$helper->name_controller = $this->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
		 
		// Language
		$helper->default_form_language = $default_lang;
		$helper->allow_employee_form_lang = $default_lang;
		 
		// Title and toolbar
		$helper->title = $this->displayName;
		$helper->show_toolbar = true;        // false -> remove toolbar
		$helper->toolbar_scroll = true;      // yes - > Toolbar is always visible on the top of the screen.
		$helper->submit_action = 'submit'.$this->name;
		$helper->toolbar_btn = array(
			'save' =>
			array(
				'desc' => $this->l('Save'),
				'href' => AdminController::$currentIndex.'&configure='.$this->name.'&save'.$this->name.
				'&token='.Tools::getAdminTokenLite('AdminModules'),
			),
			'back' => array(
				'href' => AdminController::$currentIndex.'&token='.Tools::getAdminTokenLite('AdminModules'),
				'desc' => $this->l('Back to list')
			)
		);
		 
		// Load current value
		$helper->fields_value['MYMODULE_NAME'] = Configuration::get('MYMODULE_NAME');
		 
		return $helper->generateForm($fields_form);
	}
	
	
	
}