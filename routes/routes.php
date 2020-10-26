<?php

if(!$_POST && file_get_contents("php://input")) {
  $_POST = json_decode(file_get_contents("php://input"), true);
} 

// CLIENT "ROUTE"
  if(!empty($_GET) && $_GET['area'] == 'clients') {

    require_once('app/Client.php');

    $client = new Client();
    
    if(!empty($_GET['id'])) {
      
      if(!empty($_POST)) {
        
        if(!empty($_GET['action']) && $_GET['action'] == 'update') {

          echo $client->update($_GET['id'], $_POST);
        
        }
        elseif(!empty($_GET['action']) && $_GET['action'] == 'delete') {
        
          echo $client->delete($_GET['id']);
        
        }

      }
      else {
        
        echo $client->find($_GET['id']);
      
      }

    }
    else 
    {

      if(!empty($_POST)) {

        echo $client->create($_POST);

      }
      else {

        echo $client->all();

      }
    
    }
  
  }

  // OWNER "ROUTE"
  if(!empty($_GET) && $_GET['area'] == 'owners') {

    require_once('app/Owner.php');

    $owner = new Owner();
    
    if(!empty($_GET['id'])) {
      
      if(!empty($_POST)) {
        
        if(!empty($_GET['action']) && $_GET['action'] == 'update') {

          echo $owner->update($_GET['id'], $_POST);
        
        }

      }
      else {
        
        echo $owner->find($_GET['id']);
      
      }

    }
    else 
    {

      if(!empty($_POST)) {

        echo $owner->create($_POST);

      }
      else {

        echo $owner->all();

      }
    
    }
  
  }

  // PROPERTY "ROUTE"
  if(!empty($_GET) && $_GET['area'] == 'propertys') {

    require_once('app/Property.php');

    $property = new Property();
    
    if(!empty($_GET['id'])) {
      
      if(!empty($_POST)) {
        
        if(!empty($_GET['action']) && $_GET['action'] == 'update') {

          echo $property->update($_GET['id'], $_POST);
        
        }

      }
      else {
        
        echo $property->find($_GET['id']);
      
      }

    }
    else 
    {

      if(!empty($_POST)) {

        echo $property->create($_POST);

      }
      else {

        echo $property->all();

      }
    
    }
  
  }

  // CONTRACT "ROUTE"
  if(!empty($_GET) && $_GET['area'] == 'contracts') {

    require_once('app/Contract.php');

    $contract = new Contract();
    
    if(!empty($_GET['id'])) {
      
      if(!empty($_POST)) {
        
        if(!empty($_GET['action']) && $_GET['action'] == 'update') {

          echo $contract->update($_GET['id'], $_POST);
        
        }

      }
      else {
        
        echo $contract->find($_GET['id']);
      
      }

    }
    else 
    {      
      if(!empty($_POST)) {
        echo $contract->create($_POST);        
      }
      else {        
        echo $contract->all();
      }    
    }
  
  }

  // PAYMENTS "ROUTE"
  if(!empty($_GET) && $_GET['area'] == 'payments') {

    require_once('app/Payment.php');

    $payment = new Payment();
    
    if(!empty($_GET['id'])) {
      
      if(!empty($_POST)) {
        
        if(!empty($_GET['action']) && $_GET['action'] == 'update') {
          
          echo $payment->update($_GET['id'], $_POST);
        
        }

      }
      else {
        
        echo $payment->find($_GET['id']);
      
      }

    }
    else 
    {

      if(!empty($_POST)) {
        echo $payment->create($_POST);

      }
      else {

        echo $payment->all();

      }
    
    }
  
  }