<?php
/**
 * BaseModel - Modelo base con funciones comunes
 */

namespace Api\Core;

use Api\Core\Database;
use Exception;

abstract class BaseModel {
    protected $db;
    protected $table;
    protected $primaryKey = 'id';
    protected $fillable = [];
    protected $hidden = [];
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function findAll() {
        $sql = "SELECT * FROM {$this->table} ORDER BY {$this->primaryKey} DESC";
        return $this->db->fetchAll($sql);
    }
    
    public function findById($id) {
        $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id LIMIT 1";
        return $this->db->fetch($sql, ['id' => $id]);
    }
    
    public function create($data) {
        $data = $this->filterFillable($data);
        
        if (empty($data)) {
            throw new Exception('No hay datos válidos para crear el registro');
        }
        
        $lastId = $this->db->insert($this->table, $data);
        return $this->findById($lastId);
    }
    
    public function update($id, $data) {
        $data = $this->filterFillable($data);
        
        if (empty($data)) {
            throw new Exception('No hay datos válidos para actualizar el registro');
        }
        
        $this->db->update($this->table, $data, "{$this->primaryKey} = :id", ['id' => $id]);
        return $this->findById($id);
    }
    
    public function delete($id) {
        return $this->db->delete($this->table, "{$this->primaryKey} = :id", ['id' => $id]);
    }
    
    public function exists($id) {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE {$this->primaryKey} = :id";
        $result = $this->db->fetch($sql, ['id' => $id]);
        return $result['count'] > 0;
    }
    
    public function count() {
        $sql = "SELECT COUNT(*) as count FROM {$this->table}";
        $result = $this->db->fetch($sql);
        return (int)$result['count'];
    }
    
    public function paginate($page = 1, $limit = 10, $conditions = '', $params = []) {
        $offset = ($page - 1) * $limit;
        
        // Contar total de registros
        $countSql = "SELECT COUNT(*) as total FROM {$this->table}";
        if (!empty($conditions)) {
            $countSql .= " WHERE {$conditions}";
        }
        
        $totalResult = $this->db->fetch($countSql, $params);
        $total = (int)$totalResult['total'];
        
        // Obtener registros paginados
        $sql = "SELECT * FROM {$this->table}";
        if (!empty($conditions)) {
            $sql .= " WHERE {$conditions}";
        }
        $sql .= " ORDER BY {$this->primaryKey} DESC LIMIT {$limit} OFFSET {$offset}";
        
        $records = $this->db->fetchAll($sql, $params);
        
        return [
            'data' => $records,
            'pagination' => [
                'page' => $page,
                'limit' => $limit,
                'total' => $total,
                'pages' => ceil($total / $limit),
                'has_next' => $page < ceil($total / $limit),
                'has_prev' => $page > 1
            ]
        ];
    }
    
    protected function filterFillable($data) {
        if (empty($this->fillable)) {
            return $data;
        }
        
        $filtered = [];
        foreach ($this->fillable as $field) {
            if (array_key_exists($field, $data)) {
                $filtered[$field] = $data[$field];
            }
        }
        
        return $filtered;
    }
    
    protected function removeHidden($data) {
        if (empty($this->hidden) || !is_array($data)) {
            return $data;
        }
        
        foreach ($this->hidden as $field) {
            unset($data[$field]);
        }
        
        return $data;
    }
    
    public function toArray($data) {
        if (!$data) {
            return null;
        }
        
        return $this->removeHidden($data);
    }
    
    // ✅ Helper para transacciones
    protected function transaction(callable $callback) {
        $this->db->beginTransaction();
        
        try {
            $result = $callback();
            $this->db->commit();
            return $result;
        } catch (Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }
}