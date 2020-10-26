<?php

  require_once('Connection.php');

  class Client extends Connection {

    public function all() {

      $response = [];

      $conn = $this->getConnection();
  
      $result = $conn->query(
        "SELECT * FROM clients"
      );

      $fetchAll = $result->fetchAll();

      foreach ($fetchAll as $content)
      {
          $response[] = [
            'id' => (int) $content['id'],
            'telefone' => $content['telefone'],
            'email' => $content['email'],
          ];
      }
    
      return json_encode($response);
      
    }

    public function find($id) {

      $response = [];
      
      $conn = $this->getConnection();

      $result = $conn->prepare("SELECT * FROM clients WHERE id=:id");

      $result->execute([':id'=>$id]);

      $fetch = $result->fetch();

      $response = [
        'id' => (int) $fetch['id'],
        'telefone' => $fetch['telefone'],
        'email' => $fetch['email'],
      ];
      
      return json_encode($response);

    }

    public function create($request) {

      $conn = $this->getConnection();
          
      $sql = "INSERT INTO clients (telefone, email)
              VALUES (:telefone, :email)";

      $result = $conn->prepare($sql);

      try {

        $conn->beginTransaction();

        $result->execute([
          ':telefone' => $request['telefone'],
          ':email' => $request['email'],
        ]);
        
        $id = $conn->lastInsertId();
        
        $conn->commit();

        return $this->find($id);

      } catch (Exception $e){
          $conn->rollback();
          throw $e;

          return json_encode([
            'message' => 'Erro ao criar',
          ]);
      }      

    }

  }