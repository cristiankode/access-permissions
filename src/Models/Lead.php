<?php

namespace App\Models;

use App\Connection\Connection as DBConnection;
use App\Dtos\LeadDto;
use PDO;

/**
 * @OA\Info(title="Newsletter PT API", version="0.1")
 */
class Lead 
{
    protected DBConnection $connection;

    public function __construct()
    {
        $this->connection = new DBConnection();
    }

    /**
     * @OA\Post(
     *     path="/leads/index.php",
     *     summary="Method for store leads newslatter Plaza de la Tecnología",
     *     tags={"Leads"},
     *     @OA\RequestBody(
     *      @OA\MediaType(
     *          mediaType="multipart/form-data",
     *          @OA\Schema(
     *              @OA\Property(
     *                  property="email",
     *                  type="string"
     *              ),
     *          ),
     *      ),
     *     ),
     *     @OA\Response(response="201", description="Item was created"),
     *     @OA\Response(response="503", description="Error Server")
     * )
     */
    public function store(LeadDto $leadDto)
    {
        $db = $this->connection->open();
        try {
            $stmt = $db->prepare("INSERT INTO leads (email, state_code_id, created_at) VALUES (:email, :state_code_id, :created_at)");
            $stmt->execute([
                ':email' => $leadDto->getEmail(),
                ':state_code_id' => $leadDto->getStateCodeId(),
                ':created_at' => $leadDto->getCreatedAt()
            ]);
            return $stmt;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function findWhereDate(string $initDate, string $endDate)
    { 
        $db = $this->connection->open();
        try {
            $query = "SELECT DISTINCT l.email as correo, sc.name as plaza, l.created_at as fecha FROM leads l JOIN state_codes sc on sc.id = l.state_code_id WHERE l.created_at BETWEEN '{$initDate}' AND '{$endDate}'";
            $stmt = $db->prepare($query, [
                PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL,
            ]);
            $result = $stmt->execute();
        
            return $stmt;
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}