<?php

  require_once('Connection.php');

  class Owner extends Connection {

    public function create($request) {

      $conn = $this->getConnection();
          
      $sql = "INSERT INTO owners (name, telefone, date_transfer)
              VALUES (:name, :telefone, :date_transfer)";

      $result = $conn->prepare($sql);

      try {

        $conn->beginTransaction();

        $result->execute([
          ':telefone' => $request['telefone'],
          ':name' => $request['name'],
          ':date_transfer' => $request['date_transfer'],
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
        "SELECT * FROM owners"
      );

      $fetchAll = $result->fetchAll();

      foreach ($fetchAll as $content)
      {
          $response[] = [
            'id' => (int) $content['id'],
            'name' => $content['name'],
            'telefone' => $content['telefone'],
            'date_transfer' => $content['date_transfer'],
          ];
      }
    
      return json_encode($response);
      
    }

    public function find($id) {

      $response = [];
      
      $conn = $this->getConnection();

      $result = $conn->prepare("SELECT * FROM owners WHERE id=:id");

      $result->execute([':id'=>$id]);

      $fetch = $result->fetch();

      $response = [
        'id' => (int) $fetch['id'],
        'telefone' => $fetch['telefone'],
        'name' => $fetch['name'],
      ];
      
      return json_encode($response);

    }  

  }