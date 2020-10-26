<?php

  require_once('Connection.php');
  require_once('Payment.php');

  class Contract extends Connection {

    public function create($request) {
      
      $conn = $this->getConnection();

      $sql = "INSERT INTO contracts 
              (client_id, owner_id, property_id, date_start, date_end, administration_fee, value_rent, value_condominium, value_tax)
              VALUES (:client_id, :owner_id, :property_id, :date_start, :date_end, :administration_fee, :value_rent, :value_condominium, :value_tax)";

      $result = $conn->prepare($sql);

      try {

        $conn->beginTransaction();

        $result->execute([
          ':client_id' => $request['client_id'],
          ':owner_id' => $request['owner_id'],
          ':property_id' => $request['property_id'],
          ':date_start' => $request['date_start'],
          ':date_end' => $request['date_end'],
          ':administration_fee' => $request['administration_fee'],
          ':value_rent' => $request['value_rent'],
          ':value_condominium' => $request['value_condominium'],
          ':value_tax' => $request['value_tax'],
        ]);
        
        $id = $conn->lastInsertId();
        
        $conn->commit();

        $contract = json_decode($this->find($id), true);

        $date_payment = $contract['date_start'];
        
        // GERAR PARCELAS PARA OS PRÓXIMOS 12 MESES 
        for($x = 1; $x <= 12; $x++) {
          
          $payment = new Payment();

          // SE PRIMEIRA MENSALIDADE
          if($x == 1) {

            // (INICIO CONTRATO)
            $now = $date_payment;

            // VERIFICA SE PRIMEIRA MENSALIDADE
            $d = new DateTime($date_payment);
            $d->modify('first day of this month');
            $first_day_month = $d->format('Y-m-d 00:00:00');

            if($date_payment != $first_day_month) {
             
              // ULTIMO DIA DO MÊS
              $last_day_month = date("Y-m-t 00:00:00", strtotime($date_payment));

              // DATA DE PAGAMENTO TORNA O PRIMEIRO DIA DO MÊS
              $date_payment = date('Y-m-d 00:00:00', strtotime( $last_day_month. ' + 1 day')); 

              // GERA O VALOR PROPORCIONAL AOS DIAS UTILIZADOS
              $day_first = new DateTime($now);
              $day_last = new DateTime($last_day_month);
              
              // TOTAL DE DIAS A PAGAR
              $quant_days_to_pay = $day_last->diff($day_first)->format("%a");

              // TOTAL DE DIAS NO MÊS ATUAL
              $total_days_in_month = date('t', strtotime($now));

              // VALOR TOTAL DA MENSALIDADE
              $total_value_rent_month = ($contract['value_rent'] + $contract['value_condominium'] + $contract['value_tax']);

              // VALOR DIÁRIO DA MENSALIDADE
              $total_value_rent_day = $total_value_rent_month / $total_days_in_month;

              // VALOR PROPORCIONAL DA PRIMEIRA MENSALIDADE
              $value_total = $total_value_rent_day * $quant_days_to_pay;
            
            }           

          }
          else {
            $value_total = ($contract['value_rent'] + $contract['value_condominium'] + $contract['value_tax']);
          }

          $paymentData = [
            'contract_id' => $contract['id'],
            'value_rent' => $contract['value_rent'],
            'value_condominium' => $contract['value_condominium'],
            'value_tax' => $contract['value_tax'],
            'value_total' => $value_total,
            'administration_fee' => $contract['administration_fee'],
            'paid_at' => null,
            'transfer_at' => null,
            'value_transfer' => 0,
            'date_payment' => $date_payment,
          ];
          
          // SALVA PAGAMENTO
          $payment->create($paymentData);
          
          // INCREMENTA DATA
          $date_payment = date("Y-m-d 00:00:00", strtotime("+1 month", strtotime($date_payment)));
          
        }

        return json_encode($contract);

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
        "SELECT * FROM contracts"
      );

      $fetchAll = $result->fetchAll();

      foreach ($fetchAll as $content)
      {

          if($this->getPayments($content['id'])) {
            $payments = $this->getPayments($content['id']);
          }
          else {
            $payments = null;
          }

          $response[] = [
            'id' => (int) $content['id'],
            'client_id' => $content['client_id'],
            'owner_id' => $content['owner_id'],
            'proprety_id' => $content['proprety_id'],
            'date_start' => $content['date_start'],
            'date_end' => $content['date_end'],
            'administration_fee' => $content['administration_fee'],
            'value_rent' => $content['value_rent'],
            'value_condominium' => $content['value_condominium'],
            'value_tax' => $content['value_tax'],
            'payments' => $payments
          ];

      }
    
      return json_encode($response);
      
    }

    public function find($id) {

      $response = [];
      
      $conn = $this->getConnection();

      $result = $conn->prepare("SELECT * FROM contracts WHERE id=:id");

      $result->execute([':id'=>$id]);

      $fetch = $result->fetch();

      $response = [
        'id' => (int) $fetch['id'],
        'client_id' => $fetch['client_id'],
        'owner_id' => $fetch['owner_id'],
        'proprety_id' => $fetch['proprety_id'],
        'date_start' => $fetch['date_start'],
        'date_end' => $fetch['date_end'],
        'administration_fee' => $fetch['administration_fee'],
        'value_rent' => $fetch['value_rent'],
        'value_condominium' => $fetch['value_condominium'],
        'value_tax' => $fetch['value_tax'],
      ];
     
      return json_encode($response);

    }

    public function update($id, $request) {

      $conn = $this->getConnection();
          
      $sql = "UPDATE contracts 
              SET telefone = :telefone, email = :email
              WHERE id = :id";

      $result = $conn->prepare($sql);

      $result->execute([
        ':client_id' => $request['client_id'],
        ':owner_id' => $request['owner_id'],
        ':property_id' => $request['property_id'],
        ':date_start' => $request['date_start'],
        ':date_end' => $request['date_end'],
        ':administration_fee' => $request['administration_fee'],
        ':value_rent' => $request['value_rent'],
        ':value_condominium' => $request['value_condominium'],
        ':value_tax' => $request['value_tax'],
      ]);

      return $this->find($id);

    }

    public function getPayments($contract_id) {

      $response = [];
      
      $conn = $this->getConnection();

      $result = $conn->prepare("SELECT * FROM payments WHERE contract_id=:id");

      $result->execute([':id'=>$contract_id]);

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
     
      return $response;

    }

  }