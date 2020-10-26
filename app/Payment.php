<?php

  require_once('Connection.php');

  class Payment extends Connection {

    public function all() {

      $response = [];

      $conn = $this->getConnection();
  
      $result = $conn->query(
        "SELECT * FROM payments"
      );

      $fetchAll = $result->fetchAll();

      foreach ($fetchAll as $fetch)
      {

          $response[] = [
            'id' => (int) $fetch['id'],
            'contract_id' => $fetch['contract_id'],
            'value_rent' => $fetch['value_rent'],
            'value_condominium' => $fetch['value_condominium'],
            'value_tax' => $fetch['value_tax'],
            'administration_fee' => $fetch['administration_fee'],
            'paid_at' => $fetch['paid_at'],
            'transfer_at' => $fetch['transfer_at'],
            'value_transfer' => $fetch['value_transfer'],
            'date_payment' => $fetch['date_payment'],
          ];

      }
    
      return json_encode($response);
      
    }

    public function find($id) {

      $response = [];
      
      $conn = $this->getConnection();

      $result = $conn->prepare("SELECT * FROM payments WHERE id=:id");

      $result->execute([':id'=>$id]);

      $fetch = $result->fetch();

      $response = [
        'id' => (int) $fetch['id'],
        'contract_id' => $fetch['contract_id'],
        'value_rent' => $fetch['value_rent'],
        'value_condominium' => $fetch['value_condominium'],
        'value_tax' => $fetch['value_tax'],
        'administration_fee' => $fetch['administration_fee'],
        'paid_at' => $fetch['paid_at'],
        'transfer_at' => $fetch['transfer_at'],
        'value_transfer' => $fetch['value_transfer'],
        'date_payment' => $fetch['date_payment'],
      ];
      
      return json_encode($response);

    }

    public function create($request) {

      $conn = $this->getConnection();
          
      $sql = "INSERT INTO payments 
              (contract_id, value_rent, value_condominium, value_tax, administration_fee, paid_at, value_transfer, date_payment, transfer_at, value_total)
              VALUES 
              (:contract_id, :value_rent, :value_condominium, :value_tax, :administration_fee, :paid_at, :value_transfer, :date_payment, :transfer_at, :value_total)";

      $result = $conn->prepare($sql);

      try {

        $conn->beginTransaction();

        $result->execute([
          ':contract_id' => $request['contract_id'],
          ':date_payment' => $request['date_payment'],
          ':value_rent' => $request['value_rent'],
          ':value_condominium' => $request['value_condominium'],
          ':value_tax' => $request['value_tax'],
          ':administration_fee' => $request['administration_fee'],
          ':paid_at' => $request['paid_at'],
          ':value_transfer' => $request['value_transfer'],
          ':transfer_at' => $request['transfer_at'],
          ':value_total' => $request['value_total'],
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

    public function update($id, $request) {

      $conn = $this->getConnection();
          
      $sql = "UPDATE payments 
              SET paid_at = :paid_at, transfer_at = :transfer_at
              WHERE id = :id";

      $result = $conn->prepare($sql);

      $result->execute([
        ':paid_at' => $request['paid_at'],
        ':transfer_at' => $request['transfer_at'],
        ':id' => $id,
      ]);

      return $this->find($id);

    }

  }