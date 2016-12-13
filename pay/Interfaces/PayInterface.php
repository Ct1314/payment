<?php
namespace Pay\Interfaces;

interface PayInterface
{
	/**
	* Constructor
	* @param array pay_info 
	* @param array order_info
	* @access public
	*/
	public function __construct();

	/**
	* pay
	* @param array order_info
	* @access public
	*/
	public function pay(array $order_info);
	/**
	* getPayStatus 
	* @access public
	*/
	public function getPayStatus();

	/**
	* verifyNotify 
	* @access public
	*/
	public function verifyNotify();

	/**
	* verifyReturn 
	* @access public
	*/
	public function verifyReturn();

	/**
	* sign
	* @param parameter
	* @access public
	*/
	public function sign($param);

	/**
	* request
	* @access public
	*/
	public function request();
}
