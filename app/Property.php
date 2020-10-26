<?php

  require_once('Connection.php');

  class Property extends Connection {

    public function create($request) {

      $conn = $this->getConnection();
          
      $sql = "INSERT INTO propertys (address, owner_id)
              VALUES (:address, :owner_id)";

      $result = $conn->prepare($sql);

      try {

        $conn->beginTransaction();

        $result->execute([
          ':address' => $request['address'],
          ':owner_id' => $request['owner_id'],
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

    public function all() {

      $response = [];

      $conn = $this->getConnection();
  
      $result = $conn->query(
        "SELECT * FROM propertys"
      );

      $fetchAll = $result->fetchAll();

      foreach ($fetchAll as $content)
      {
          $response[] = [
            'id' => (int) $content['id'],
            'owner_id' => $content['owner_id'],
            'address' => $content['address'],
          ];
      }
    
      return json_encode($response);
      
    }

    public function find($id) {

      $response = [];
      
      $conn = $this->getConnection();

      $result = $conn->prepare("SELECT * FROM propertys WHERE id=:id");

      $result->execute([':id'=>$id]);

      $fetch = $result->fetch();

      $response = [
        'id' => (int) $fetch['id'],
        'owner_id' => $fetch['owner_id'],
        'address' => $fetch['address'],
      ];
      
      return json_encode($response);

    }  

  }