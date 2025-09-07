<?php
// Arquivo: app/models/PerfilCandidato.php

class PerfilCandidato {
    private $conn;

    public function __construct() {
        $this->conn = getDbConnection();
    }

    public function save($candidato_id, $titulo_perfil, $resumo, $experiencia, $educacao) {
        try {
            $stmt = $this->conn->prepare("SELECT id FROM perfis_candidato WHERE candidato_id = ?");
            $stmt->execute([$candidato_id]);
            $perfil_existente = $stmt->fetch();

            if ($perfil_existente) {
                $sql = "UPDATE perfis_candidato SET titulo_perfil = ?, resumo = ?, experiencia = ?, educacao = ? WHERE candidato_id = ?";
                $stmt = $this->conn->prepare($sql);
                return $stmt->execute([$titulo_perfil, $resumo, $experiencia, $educacao, $candidato_id]);
            } else {
                $sql = "INSERT INTO perfis_candidato (candidato_id, titulo_perfil, resumo, experiencia, educacao) VALUES (?, ?, ?, ?, ?)";
                $stmt = $this->conn->prepare($sql);
                return $stmt->execute([$candidato_id, $titulo_perfil, $resumo, $experiencia, $educacao]);
            }
        } catch(PDOException $e) {
            error_log("Erro ao salvar perfil do candidato: " . $e->getMessage());
            return false;
        }
    }

    public function findByCandidatoId($candidato_id) {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM perfis_candidato WHERE candidato_id = ?");
            $stmt->execute([$candidato_id]);
            return $stmt->fetch();
        } catch(PDOException $e) {
            error_log("Erro ao buscar perfil do candidato: " . $e->getMessage());
            return false;
        }
    }
}
?>