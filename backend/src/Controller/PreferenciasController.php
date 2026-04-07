<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\ORM\TableRegistry;

class PreferenciasController extends AppController
{
    private $table;
    
    public function initialize(): void
    {
        parent::initialize();
        $this->table = TableRegistry::getTableLocator()->get('PreferenciasUsuario');
    }
    
    // GET /preferencias/usuario/{usuarioId}
    public function getByUsuario($usuarioId = null)
    {
        // 🔥 Pega o ID de várias formas
        $userId = $usuarioId ?? $this->request->getParam('usuarioId') ?? $this->request->getQuery('usuarioId');
        
        error_log("=== GET PREFERENCIAS ===");
        error_log("usuarioId param: " . ($usuarioId ?? 'null'));
        error_log("usuarioId from request: " . ($this->request->getParam('usuarioId') ?? 'null'));
        error_log("Final userId: " . $userId);
        
        if (!$userId) {
            $this->response = $this->response->withStatus(400);
            $this->response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'ID do usuário não informado'
            ]));
            return $this->response;
        }
        
        try {
            $preferencia = $this->table->find()
                ->where(['usuario_id' => $userId])
                ->first();
            
            if (!$preferencia) {
                // Retorna preferências padrão
                $this->response->getBody()->write(json_encode([
                    'success' => true,
                    'data' => [
                        'usuario_id' => (int)$userId,
                        'tema' => 'claro',
                        'modo_daltonico' => false,
                        'notificacoes_ativas' => true,
                        'som_ativo' => true,
                        'traducao_automatica' => true,
                        'preferencia_dificuldade' => 'adaptativo',
                        'meta_diaria_minutos' => 30,
                    ]
                ]));
                return $this->response;
            }
            
            $this->response->getBody()->write(json_encode([
                'success' => true,
                'data' => $preferencia
            ]));
            return $this->response;
        } catch (\Exception $e) {
            $this->response = $this->response->withStatus(500);
            $this->response->getBody()->write(json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]));
            return $this->response;
        }
    }
    
    // POST /preferencias
    public function save()
    {
        $input = file_get_contents('php://input');
        $data = json_decode($input, true) ?: $this->request->getData();
        
        if (empty($data['usuario_id'])) {
            $this->response = $this->response->withStatus(400);
            $this->response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'ID do usuário é obrigatório'
            ]));
            return $this->response;
        }
        
        try {
            $existing = $this->table->find()
                ->where(['usuario_id' => $data['usuario_id']])
                ->first();
            
            if ($existing) {
                $entity = $this->table->patchEntity($existing, $data);
            } else {
                $entity = $this->table->newEntity($data);
            }
            
            if ($this->table->save($entity)) {
                $this->response->getBody()->write(json_encode([
                    'success' => true,
                    'message' => 'Preferências salvas com sucesso',
                    'data' => $entity
                ]));
                return $this->response;
            }
            
            $this->response = $this->response->withStatus(422);
            $this->response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Erro ao salvar preferências',
                'errors' => $entity->getErrors()
            ]));
            return $this->response;
        } catch (\Exception $e) {
            $this->response = $this->response->withStatus(500);
            $this->response->getBody()->write(json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]));
            return $this->response;
        }
    }
}