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
		Configuration::updateValue('MYMODULE_NAME', 'pesetacoin');
		
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
}