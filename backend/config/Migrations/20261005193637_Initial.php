<?php
declare(strict_types=1);

use Migrations\BaseMigration;
use Migrations\Db\Literal;

class Initial extends BaseMigration
{
    /**
     * Up Method.
     *
     * More information on this method is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-up-method
     *
     * @return void
     */
    public function up(): void
    {
        $this->table('assinaturas')
            ->addColumn('usuario_id', 'integer', [
                'default' => null,
                'limit' => 10,
                'null' => true,
            ])
            ->addColumn('plano_id', 'integer', [
                'default' => null,
                'limit' => 10,
                'null' => true,
            ])
            ->addColumn('status', 'string', [
                'default' => 'pending',
                'limit' => 20,
                'null' => true,
            ])
            ->addColumn('data_inicio', 'timestamp', [
                'default' => null,
                'limit' => null,
                'null' => true,
                'precision' => 6,
                'scale' => 6,
            ])
            ->addColumn('data_fim', 'timestamp', [
                'default' => null,
                'limit' => null,
                'null' => true,
                'precision' => 6,
                'scale' => 6,
            ])
            ->addColumn('data_cancelamento', 'timestamp', [
                'default' => null,
                'limit' => null,
                'null' => true,
                'precision' => 6,
                'scale' => 6,
            ])
            ->addColumn('payment_id', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('payment_gateway', 'string', [
                'default' => null,
                'limit' => 50,
                'null' => true,
            ])
            ->addColumn('is_ativo', 'boolean', [
                'default' => true,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('created_at', 'timestamp', [
                'default' => Literal::from('now()'),
                'limit' => null,
                'null' => true,
                'precision' => 6,
                'scale' => 6,
            ])
            ->addColumn('updated_at', 'timestamp', [
                'default' => Literal::from('now()'),
                'limit' => null,
                'null' => true,
                'precision' => 6,
                'scale' => 6,
            ])
            ->create();

        $this->table('flashcard_tags')
            ->addColumn('flashcard_id', 'integer', [
                'default' => null,
                'limit' => 10,
                'null' => true,
            ])
            ->addColumn('tag_id', 'integer', [
                'default' => null,
                'limit' => 10,
                'null' => true,
            ])
            ->addColumn('criado_em', 'timestamp', [
                'default' => 'CURRENT_TIMESTAMP',
                'limit' => null,
                'null' => true,
                'precision' => 6,
                'scale' => 6,
            ])
            ->create();

        $this->table('flashcards')
            ->addColumn('usuario_id', 'integer', [
                'default' => null,
                'limit' => 10,
                'null' => true,
            ])
            ->addColumn('frente', 'text', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('verso', 'text', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('nivel_dificuldade', 'string', [
                'default' => 'iniciante',
                'limit' => 20,
                'null' => true,
            ])
            ->addColumn('criado_em', 'timestamp', [
                'default' => 'CURRENT_TIMESTAMP',
                'limit' => null,
                'null' => true,
                'precision' => 6,
                'scale' => 6,
            ])
            ->addColumn('atualizado_em', 'timestamp', [
                'default' => 'CURRENT_TIMESTAMP',
                'limit' => null,
                'null' => true,
                'precision' => 6,
                'scale' => 6,
            ])
            ->addColumn('uuid', 'uuid', [
                'default' => Literal::from('gen_random_uuid()'),
                'limit' => null,
                'null' => false,
            ])
            ->addIndex(
                $this->index('uuid')
                    ->setName('flashcards_uuid_unique')
                    ->setType('unique')
            )
            ->create();

        $this->table('frases_semelhantes')
            ->addColumn('prompt_id', 'integer', [
                'default' => null,
                'limit' => 10,
                'null' => true,
            ])
            ->addColumn('frase_semelhante', 'text', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('pontuacao_semelhante', 'decimal', [
                'default' => null,
                'null' => true,
                'precision' => 3,
                'scale' => 2,
            ])
            ->addColumn('tipo_frase', 'string', [
                'default' => 'relacionada',
                'limit' => 20,
                'null' => true,
            ])
            ->addColumn('nivel_dificuldade', 'string', [
                'default' => 'iniciante',
                'limit' => 20,
                'null' => true,
            ])
            ->addColumn('criado_em', 'timestamp', [
                'default' => 'CURRENT_TIMESTAMP',
                'limit' => null,
                'null' => true,
                'precision' => 6,
                'scale' => 6,
            ])
            ->create();

        $this->table('imagens_geradas')
            ->addColumn('prompt_id', 'integer', [
                'default' => null,
                'limit' => 10,
                'null' => true,
            ])
            ->addColumn('traducao_id', 'integer', [
                'default' => null,
                'limit' => 10,
                'null' => true,
            ])
            ->addColumn('url_imagem', 'string', [
                'default' => null,
                'limit' => 500,
                'null' => true,
            ])
            ->addColumn('prompt_imagem', 'text', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('servico_geracao', 'string', [
                'default' => null,
                'limit' => 50,
                'null' => true,
            ])
            ->addColumn('qualidade_imagem', 'string', [
                'default' => 'media',
                'limit' => 20,
                'null' => true,
            ])
            ->addColumn('tamanho_arquivo', 'integer', [
                'default' => null,
                'limit' => 10,
                'null' => true,
            ])
            ->addColumn('dimensoes', 'string', [
                'default' => null,
                'limit' => 20,
                'null' => true,
            ])
            ->addColumn('criado_em', 'timestamp', [
                'default' => 'CURRENT_TIMESTAMP',
                'limit' => null,
                'null' => true,
                'precision' => 6,
                'scale' => 6,
            ])
            ->create();

        $this->table('logs_permissoes')
            ->addColumn('usuario_id', 'integer', [
                'default' => null,
                'limit' => 10,
                'null' => true,
            ])
            ->addColumn('acao', 'string', [
                'default' => null,
                'limit' => 100,
                'null' => true,
            ])
            ->addColumn('entidade', 'string', [
                'default' => null,
                'limit' => 100,
                'null' => true,
            ])
            ->addColumn('entidade_id', 'integer', [
                'default' => null,
                'limit' => 10,
                'null' => true,
            ])
            ->addColumn('dados_anteriores', 'json', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('dados_novos', 'json', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('ip', 'string', [
                'default' => null,
                'limit' => 45,
                'null' => true,
            ])
            ->addColumn('user_agent', 'text', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('created_at', 'timestamp', [
                'default' => Literal::from('now()'),
                'limit' => null,
                'null' => true,
                'precision' => 6,
                'scale' => 6,
            ])
            ->create();

        $this->table('permissoes')
            ->addColumn('nome', 'string', [
                'default' => null,
                'limit' => 100,
                'null' => false,
            ])
            ->addColumn('descricao', 'text', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('recurso', 'string', [
                'default' => null,
                'limit' => 100,
                'null' => true,
            ])
            ->addColumn('acao', 'string', [
                'default' => null,
                'limit' => 50,
                'null' => true,
            ])
            ->addColumn('is_ativo', 'boolean', [
                'default' => true,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('created_at', 'timestamp', [
                'default' => Literal::from('now()'),
                'limit' => null,
                'null' => true,
                'precision' => 6,
                'scale' => 6,
            ])
            ->addIndex(
                $this->index('nome')
                    ->setName('permissoes_nome_key')
                    ->setType('unique')
            )
            ->create();

        $this->table('planos')
            ->addColumn('nome', 'string', [
                'default' => null,
                'limit' => 50,
                'null' => false,
            ])
            ->addColumn('descricao', 'text', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('role_id', 'integer', [
                'default' => null,
                'limit' => 10,
                'null' => true,
            ])
            ->addColumn('preco_mensal', 'decimal', [
                'default' => '0',
                'null' => true,
                'precision' => 10,
                'scale' => 2,
            ])
            ->addColumn('preco_anual', 'decimal', [
                'default' => '0',
                'null' => true,
                'precision' => 10,
                'scale' => 2,
            ])
            ->addColumn('dias_trial', 'integer', [
                'default' => '0',
                'limit' => 10,
                'null' => true,
            ])
            ->addColumn('recursos', 'json', [
                'default' => '[]',
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('limites', 'json', [
                'default' => '{}',
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('is_ativo', 'boolean', [
                'default' => true,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('ordem', 'integer', [
                'default' => '0',
                'limit' => 10,
                'null' => true,
            ])
            ->addColumn('created_at', 'timestamp', [
                'default' => Literal::from('now()'),
                'limit' => null,
                'null' => true,
                'precision' => 6,
                'scale' => 6,
            ])
            ->addColumn('updated_at', 'timestamp', [
                'default' => Literal::from('now()'),
                'limit' => null,
                'null' => true,
                'precision' => 6,
                'scale' => 6,
            ])
            ->create();

        $this->table('preferencias_usuario')
            ->addColumn('usuario_id', 'integer', [
                'default' => null,
                'limit' => 10,
                'null' => true,
            ])
            ->addColumn('tema', 'string', [
                'default' => 'claro',
                'limit' => 20,
                'null' => true,
            ])
            ->addColumn('modo_daltonico', 'boolean', [
                'default' => false,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('notificacoes_ativas', 'boolean', [
                'default' => true,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('som_ativo', 'boolean', [
                'default' => true,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('traducao_automatica', 'boolean', [
                'default' => true,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('preferencia_dificuldade', 'string', [
                'default' => 'adaptativo',
                'limit' => 20,
                'null' => true,
            ])
            ->addColumn('meta_diaria_minutos', 'integer', [
                'default' => '30',
                'limit' => 10,
                'null' => true,
            ])
            ->addColumn('criado_em', 'timestamp', [
                'default' => 'CURRENT_TIMESTAMP',
                'limit' => null,
                'null' => true,
                'precision' => 6,
                'scale' => 6,
            ])
            ->addColumn('atualizado_em', 'timestamp', [
                'default' => 'CURRENT_TIMESTAMP',
                'limit' => null,
                'null' => true,
                'precision' => 6,
                'scale' => 6,
            ])
            ->addIndex(
                $this->index('usuario_id')
                    ->setName('preferencias_usuario_usuario_id_key')
                    ->setType('unique')
            )
            ->create();

        $this->table('progresso_usuario')
            ->addColumn('usuario_id', 'integer', [
                'default' => null,
                'limit' => 10,
                'null' => true,
            ])
            ->addColumn('vocabulario_aprendido', 'integer', [
                'default' => '0',
                'limit' => 10,
                'null' => true,
            ])
            ->addColumn('flashcards_concluidos', 'integer', [
                'default' => '0',
                'limit' => 10,
                'null' => true,
            ])
            ->addColumn('quizes_concluidos', 'integer', [
                'default' => '0',
                'limit' => 10,
                'null' => true,
            ])
            ->addColumn('tempo_total_estudo', 'integer', [
                'default' => '0',
                'limit' => 10,
                'null' => true,
            ])
            ->addColumn('sequencia_atual', 'integer', [
                'default' => '0',
                'limit' => 10,
                'null' => true,
            ])
            ->addColumn('maior_sequencia', 'integer', [
                'default' => '0',
                'limit' => 10,
                'null' => true,
            ])
            ->addColumn('ultima_atividade', 'timestamp', [
                'default' => null,
                'limit' => null,
                'null' => true,
                'precision' => 6,
                'scale' => 6,
            ])
            ->addColumn('progresso_nivel', 'json', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('atualizado_em', 'timestamp', [
                'default' => 'CURRENT_TIMESTAMP',
                'limit' => null,
                'null' => true,
                'precision' => 6,
                'scale' => 6,
            ])
            ->addIndex(
                $this->index('usuario_id')
                    ->setName('progresso_usuario_usuario_id_key')
                    ->setType('unique')
            )
            ->addIndex(
                $this->index('usuario_id')
                    ->setName('idx_progresso_usuario')
            )
            ->create();

        $this->table('prompts')
            ->addColumn('usuario_id', 'integer', [
                'default' => null,
                'limit' => 10,
                'null' => true,
            ])
            ->addColumn('texto_original', 'text', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('idioma_original', 'string', [
                'default' => 'pt-BR',
                'limit' => 10,
                'null' => true,
            ])
            ->addColumn('contexto', 'string', [
                'default' => 'manual',
                'limit' => 20,
                'null' => true,
            ])
            ->addColumn('midia_origem_id', 'integer', [
                'default' => null,
                'limit' => 10,
                'null' => true,
            ])
            ->addColumn('sessao_id', 'string', [
                'default' => null,
                'limit' => 100,
                'null' => true,
            ])
            ->addColumn('criado_em', 'timestamp', [
                'default' => 'CURRENT_TIMESTAMP',
                'limit' => null,
                'null' => true,
                'precision' => 6,
                'scale' => 6,
            ])
            ->addColumn('uuid', 'uuid', [
                'default' => Literal::from('gen_random_uuid()'),
                'limit' => null,
                'null' => false,
            ])
            ->addIndex(
                $this->index('uuid')
                    ->setName('prompts_uuid_key')
                    ->setType('unique')
            )
            ->create();

        $this->table('quizes')
            ->addColumn('usuario_id', 'integer', [
                'default' => null,
                'limit' => 10,
                'null' => true,
            ])
            ->addColumn('titulo', 'string', [
                'default' => null,
                'limit' => 200,
                'null' => true,
            ])
            ->addColumn('descricao', 'text', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('tipo_criacao', 'string', [
                'default' => 'ia_gerado',
                'limit' => 20,
                'null' => true,
            ])
            ->addColumn('nivel_dificuldade', 'string', [
                'default' => 'iniciante',
                'limit' => 20,
                'null' => true,
            ])
            ->addColumn('total_questoes', 'integer', [
                'default' => null,
                'limit' => 10,
                'null' => true,
            ])
            ->addColumn('tempo_limite', 'integer', [
                'default' => null,
                'limit' => 10,
                'null' => true,
            ])
            ->addColumn('publico', 'boolean', [
                'default' => false,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('criado_em', 'timestamp', [
                'default' => 'CURRENT_TIMESTAMP',
                'limit' => null,
                'null' => true,
                'precision' => 6,
                'scale' => 6,
            ])
            ->addColumn('atualizado_em', 'timestamp', [
                'default' => 'CURRENT_TIMESTAMP',
                'limit' => null,
                'null' => true,
                'precision' => 6,
                'scale' => 6,
            ])
            ->addColumn('uuid', 'uuid', [
                'default' => Literal::from('gen_random_uuid()'),
                'limit' => null,
                'null' => false,
            ])
            ->addIndex(
                $this->index('uuid')
                    ->setName('quizes_uuid_unique')
                    ->setType('unique')
            )
            ->create();

        $this->table('respostas_usuario')
            ->addColumn('usuario_id', 'integer', [
                'default' => null,
                'limit' => 10,
                'null' => false,
            ])
            ->addColumn('tipo', 'string', [
                'default' => null,
                'limit' => 20,
                'null' => false,
            ])
            ->addColumn('referencia_id', 'integer', [
                'default' => null,
                'limit' => 10,
                'null' => false,
            ])
            ->addColumn('correto', 'boolean', [
                'default' => false,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('criado_em', 'timestamp', [
                'default' => null,
                'limit' => null,
                'null' => false,
                'precision' => 6,
                'scale' => 6,
            ])
            ->addIndex(
                $this->index('usuario_id')
                    ->setName('respostas_usuario_usuario_id')
            )
            ->addIndex(
                $this->index('tipo')
                    ->setName('respostas_usuario_tipo')
            )
            ->create();

        $this->table('role_permissoes', ['id' => false, 'primary_key' => ['role_id', 'permissao_id']])
            ->addColumn('role_id', 'integer', [
                'default' => null,
                'limit' => 10,
                'null' => false,
            ])
            ->addColumn('permissao_id', 'integer', [
                'default' => null,
                'limit' => 10,
                'null' => false,
            ])
            ->addColumn('created_at', 'timestamp', [
                'default' => Literal::from('now()'),
                'limit' => null,
                'null' => true,
                'precision' => 6,
                'scale' => 6,
            ])
            ->create();

        $this->table('roles')
            ->addColumn('nome', 'string', [
                'default' => null,
                'limit' => 50,
                'null' => false,
            ])
            ->addColumn('descricao', 'text', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('nivel', 'integer', [
                'default' => '0',
                'limit' => 10,
                'null' => true,
            ])
            ->addColumn('is_sistema', 'boolean', [
                'default' => false,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('is_ativo', 'boolean', [
                'default' => true,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('created_at', 'timestamp', [
                'default' => Literal::from('now()'),
                'limit' => null,
                'null' => true,
                'precision' => 6,
                'scale' => 6,
            ])
            ->addColumn('updated_at', 'timestamp', [
                'default' => Literal::from('now()'),
                'limit' => null,
                'null' => true,
                'precision' => 6,
                'scale' => 6,
            ])
            ->addIndex(
                $this->index('nome')
                    ->setName('roles_nome_key')
                    ->setType('unique')
            )
            ->create();

        $this->table('social_profiles')
            ->addColumn('user_id', 'integer', [
                'default' => null,
                'limit' => 10,
                'null' => false,
            ])
            ->addColumn('provider', 'string', [
                'default' => null,
                'limit' => 50,
                'null' => false,
            ])
            ->addColumn('identifier', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('access_token', 'binary', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('refresh_token', 'binary', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('token_expires', 'timestamp', [
                'default' => null,
                'limit' => null,
                'null' => true,
                'precision' => 6,
                'scale' => 6,
            ])
            ->addColumn('expires', 'timestamp', [
                'default' => null,
                'limit' => null,
                'null' => true,
                'precision' => 6,
                'scale' => 6,
            ])
            ->addColumn('created', 'timestamp', [
                'default' => 'CURRENT_TIMESTAMP',
                'limit' => null,
                'null' => true,
                'precision' => 6,
                'scale' => 6,
            ])
            ->addColumn('modified', 'timestamp', [
                'default' => 'CURRENT_TIMESTAMP',
                'limit' => null,
                'null' => true,
                'precision' => 6,
                'scale' => 6,
            ])
            ->addColumn('data', 'binary', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addIndex(
                $this->index([
                        'provider',
                        'identifier',
                    ])
                    ->setName('unique_provider_identifier')
                    ->setType('unique')
            )
            ->addIndex(
                $this->index('user_id')
                    ->setName('idx_social_profiles_user_id')
            )
            ->addIndex(
                $this->index([
                        'provider',
                        'identifier',
                    ])
                    ->setName('idx_social_profiles_provider_identifier')
            )
            ->create();

        $this->table('tags')
            ->addColumn('criado_por', 'integer', [
                'default' => null,
                'limit' => 10,
                'null' => true,
            ])
            ->addColumn('nome', 'string', [
                'default' => null,
                'limit' => 100,
                'null' => true,
            ])
            ->addColumn('cor', 'string', [
                'default' => null,
                'limit' => 7,
                'null' => true,
            ])
            ->addColumn('descricao', 'text', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('tag_sistema', 'boolean', [
                'default' => false,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('criado_em', 'timestamp', [
                'default' => 'CURRENT_TIMESTAMP',
                'limit' => null,
                'null' => true,
                'precision' => 6,
                'scale' => 6,
            ])
            ->addColumn('uuid', 'uuid', [
                'default' => Literal::from('gen_random_uuid()'),
                'limit' => null,
                'null' => false,
            ])
            ->addIndex(
                $this->index('nome')
                    ->setName('tags_nome_key')
                    ->setType('unique')
            )
            ->addIndex(
                $this->index('uuid')
                    ->setName('tags_uuid_key')
                    ->setType('unique')
            )
            ->addIndex(
                $this->index('criado_por')
                    ->setName('idx_tags_criado_por')
            )
            ->addIndex(
                $this->index('nome')
                    ->setName('idx_tags_nome')
            )
            ->create();

        $this->table('traducoes')
            ->addColumn('prompt_id', 'integer', [
                'default' => null,
                'limit' => 10,
                'null' => true,
            ])
            ->addColumn('texto_traduzido', 'text', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('idioma_destino', 'string', [
                'default' => 'en',
                'limit' => 10,
                'null' => true,
            ])
            ->addColumn('pontuacao_confianca', 'decimal', [
                'default' => null,
                'null' => true,
                'precision' => 3,
                'scale' => 2,
            ])
            ->addColumn('servico_traducao', 'string', [
                'default' => null,
                'limit' => 50,
                'null' => true,
            ])
            ->addColumn('traducoes_alternativas', 'json', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('criado_em', 'timestamp', [
                'default' => 'CURRENT_TIMESTAMP',
                'limit' => null,
                'null' => true,
                'precision' => 6,
                'scale' => 6,
            ])
            ->create();

        $this->table('users')
            ->addColumn('nome', 'string', [
                'default' => null,
                'limit' => 100,
                'null' => true,
            ])
            ->addColumn('email', 'string', [
                'default' => null,
                'limit' => 150,
                'null' => true,
            ])
            ->addColumn('senha_hash', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('telefone', 'string', [
                'default' => null,
                'limit' => 20,
                'null' => true,
            ])
            ->addColumn('nivel_ingles', 'string', [
                'default' => 'iniciante',
                'limit' => 20,
                'null' => true,
            ])
            ->addColumn('idioma_preferido', 'string', [
                'default' => 'pt-BR',
                'limit' => 10,
                'null' => true,
            ])
            ->addColumn('objetivos_aprendizado', 'text', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('status', 'string', [
                'default' => 'ativo',
                'limit' => 20,
                'null' => true,
            ])
            ->addColumn('criado_em', 'timestamp', [
                'default' => 'CURRENT_TIMESTAMP',
                'limit' => null,
                'null' => true,
                'precision' => 6,
                'scale' => 6,
            ])
            ->addColumn('atualizado_em', 'timestamp', [
                'default' => 'CURRENT_TIMESTAMP',
                'limit' => null,
                'null' => true,
                'precision' => 6,
                'scale' => 6,
            ])
            ->addColumn('ultimo_login', 'timestamp', [
                'default' => null,
                'limit' => null,
                'null' => true,
                'precision' => 6,
                'scale' => 6,
            ])
            ->addColumn('token', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('token_expires', 'timestamp', [
                'default' => null,
                'limit' => null,
                'null' => true,
                'precision' => 6,
                'scale' => 6,
            ])
            ->addColumn('uuid', 'string', [
                'default' => null,
                'limit' => 36,
                'null' => false,
            ])
            ->addColumn('role', 'string', [
                'default' => 'user',
                'limit' => 20,
                'null' => true,
            ])
            ->addColumn('foto_perfil', 'string', [
                'default' => null,
                'limit' => 500,
                'null' => true,
            ])
            ->addColumn('reset_token', 'string', [
                'default' => null,
                'limit' => 100,
                'null' => true,
            ])
            ->addColumn('reset_token_expires', 'timestamp', [
                'default' => null,
                'limit' => null,
                'null' => true,
                'precision' => 6,
                'scale' => 6,
            ])
            ->addIndex(
                $this->index('email')
                    ->setName('users_email_key')
                    ->setType('unique')
            )
            ->addIndex(
                $this->index('uuid')
                    ->setName('users_uuid')
                    ->setType('unique')
            )
            ->addIndex(
                $this->index('uuid')
                    ->setName('users_uuid_unique')
                    ->setType('unique')
            )
            ->create();

        $this->table('usuario_roles', ['id' => false, 'primary_key' => ['usuario_id', 'role_id']])
            ->addColumn('usuario_id', 'integer', [
                'default' => null,
                'limit' => 10,
                'null' => false,
            ])
            ->addColumn('role_id', 'integer', [
                'default' => null,
                'limit' => 10,
                'null' => false,
            ])
            ->addColumn('atribuido_por', 'integer', [
                'default' => null,
                'limit' => 10,
                'null' => true,
            ])
            ->addColumn('is_manual', 'boolean', [
                'default' => false,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('created_at', 'timestamp', [
                'default' => Literal::from('now()'),
                'limit' => null,
                'null' => true,
                'precision' => 6,
                'scale' => 6,
            ])
            ->create();

        $this->table('assinaturas')
            ->addForeignKey(
                $this->foreignKey('plano_id')
                    ->setReferencedTable('planos')
                    ->setReferencedColumns('id')
                    ->setOnDelete('NO_ACTION')
                    ->setOnUpdate('NO_ACTION')
                    ->setName('assinaturas_plano_id_fkey')
            )
            ->addForeignKey(
                $this->foreignKey('usuario_id')
                    ->setReferencedTable('users')
                    ->setReferencedColumns('id')
                    ->setOnDelete('CASCADE')
                    ->setOnUpdate('NO_ACTION')
                    ->setName('assinaturas_usuario_id_fkey')
            )
            ->update();

        $this->table('flashcard_tags')
            ->addForeignKey(
                $this->foreignKey('tag_id')
                    ->setReferencedTable('tags')
                    ->setReferencedColumns('id')
                    ->setOnDelete('CASCADE')
                    ->setOnUpdate('NO_ACTION')
                    ->setName('flashcard_tags_tag_id_fkey')
            )
            ->update();

        $this->table('flashcards')
            ->addForeignKey(
                $this->foreignKey('usuario_id')
                    ->setReferencedTable('users')
                    ->setReferencedColumns('id')
                    ->setOnDelete('CASCADE')
                    ->setOnUpdate('NO_ACTION')
                    ->setName('flashcards_usuario_id_fkey')
            )
            ->update();

        $this->table('frases_semelhantes')
            ->addForeignKey(
                $this->foreignKey('prompt_id')
                    ->setReferencedTable('prompts')
                    ->setReferencedColumns('id')
                    ->setOnDelete('CASCADE')
                    ->setOnUpdate('NO_ACTION')
                    ->setName('frases_semelhantes_prompt_id_fkey')
            )
            ->update();

        $this->table('imagens_geradas')
            ->addForeignKey(
                $this->foreignKey('prompt_id')
                    ->setReferencedTable('prompts')
                    ->setReferencedColumns('id')
                    ->setOnDelete('SET_NULL')
                    ->setOnUpdate('NO_ACTION')
                    ->setName('imagens_geradas_prompt_id_fkey')
            )
            ->addForeignKey(
                $this->foreignKey('traducao_id')
                    ->setReferencedTable('traducoes')
                    ->setReferencedColumns('id')
                    ->setOnDelete('SET_NULL')
                    ->setOnUpdate('NO_ACTION')
                    ->setName('imagens_geradas_traducao_id_fkey')
            )
            ->update();

        $this->table('logs_permissoes')
            ->addForeignKey(
                $this->foreignKey('usuario_id')
                    ->setReferencedTable('users')
                    ->setReferencedColumns('id')
                    ->setOnDelete('NO_ACTION')
                    ->setOnUpdate('NO_ACTION')
                    ->setName('logs_permissoes_usuario_id_fkey')
            )
            ->update();

        $this->table('planos')
            ->addForeignKey(
                $this->foreignKey('role_id')
                    ->setReferencedTable('roles')
                    ->setReferencedColumns('id')
                    ->setOnDelete('NO_ACTION')
                    ->setOnUpdate('NO_ACTION')
                    ->setName('planos_role_id_fkey')
            )
            ->update();

        $this->table('preferencias_usuario')
            ->addForeignKey(
                $this->foreignKey('usuario_id')
                    ->setReferencedTable('users')
                    ->setReferencedColumns('id')
                    ->setOnDelete('CASCADE')
                    ->setOnUpdate('NO_ACTION')
                    ->setName('preferencias_usuario_usuario_id_fkey')
            )
            ->update();

        $this->table('progresso_usuario')
            ->addForeignKey(
                $this->foreignKey('usuario_id')
                    ->setReferencedTable('users')
                    ->setReferencedColumns('id')
                    ->setOnDelete('CASCADE')
                    ->setOnUpdate('NO_ACTION')
                    ->setName('progresso_usuario_usuario_id_fkey')
            )
            ->update();

        $this->table('prompts')
            ->addForeignKey(
                $this->foreignKey('usuario_id')
                    ->setReferencedTable('users')
                    ->setReferencedColumns('id')
                    ->setOnDelete('CASCADE')
                    ->setOnUpdate('NO_ACTION')
                    ->setName('prompts_usuario_id_fkey')
            )
            ->update();

        $this->table('quizes')
            ->addForeignKey(
                $this->foreignKey('usuario_id')
                    ->setReferencedTable('users')
                    ->setReferencedColumns('id')
                    ->setOnDelete('CASCADE')
                    ->setOnUpdate('NO_ACTION')
                    ->setName('quizes_usuario_id_fkey')
            )
            ->update();

        $this->table('respostas_usuario')
            ->addForeignKey(
                $this->foreignKey('usuario_id')
                    ->setReferencedTable('users')
                    ->setReferencedColumns('id')
                    ->setOnDelete('CASCADE')
                    ->setOnUpdate('NO_ACTION')
                    ->setName('respostas_usuario_usuario_id_fkey')
            )
            ->update();

        $this->table('role_permissoes')
            ->addForeignKey(
                $this->foreignKey('permissao_id')
                    ->setReferencedTable('permissoes')
                    ->setReferencedColumns('id')
                    ->setOnDelete('CASCADE')
                    ->setOnUpdate('NO_ACTION')
                    ->setName('role_permissoes_permissao_id_fkey')
            )
            ->addForeignKey(
                $this->foreignKey('role_id')
                    ->setReferencedTable('roles')
                    ->setReferencedColumns('id')
                    ->setOnDelete('CASCADE')
                    ->setOnUpdate('NO_ACTION')
                    ->setName('role_permissoes_role_id_fkey')
            )
            ->update();

        $this->table('social_profiles')
            ->addForeignKey(
                $this->foreignKey('user_id')
                    ->setReferencedTable('users')
                    ->setReferencedColumns('id')
                    ->setOnDelete('CASCADE')
                    ->setOnUpdate('NO_ACTION')
                    ->setName('social_profiles_user_id_fkey')
            )
            ->update();

        $this->table('tags')
            ->addForeignKey(
                $this->foreignKey('criado_por')
                    ->setReferencedTable('users')
                    ->setReferencedColumns('id')
                    ->setOnDelete('SET_NULL')
                    ->setOnUpdate('NO_ACTION')
                    ->setName('tags_criado_por_fkey')
            )
            ->update();

        $this->table('traducoes')
            ->addForeignKey(
                $this->foreignKey('prompt_id')
                    ->setReferencedTable('prompts')
                    ->setReferencedColumns('id')
                    ->setOnDelete('CASCADE')
                    ->setOnUpdate('NO_ACTION')
                    ->setName('traducoes_prompt_id_fkey')
            )
            ->update();

        $this->table('usuario_roles')
            ->addForeignKey(
                $this->foreignKey('atribuido_por')
                    ->setReferencedTable('users')
                    ->setReferencedColumns('id')
                    ->setOnDelete('NO_ACTION')
                    ->setOnUpdate('NO_ACTION')
                    ->setName('usuario_roles_atribuido_por_fkey')
            )
            ->addForeignKey(
                $this->foreignKey('role_id')
                    ->setReferencedTable('roles')
                    ->setReferencedColumns('id')
                    ->setOnDelete('CASCADE')
                    ->setOnUpdate('NO_ACTION')
                    ->setName('usuario_roles_role_id_fkey')
            )
            ->addForeignKey(
                $this->foreignKey('usuario_id')
                    ->setReferencedTable('users')
                    ->setReferencedColumns('id')
                    ->setOnDelete('CASCADE')
                    ->setOnUpdate('NO_ACTION')
                    ->setName('usuario_roles_usuario_id_fkey')
            )
            ->update();
    }

    /**
     * Down Method.
     *
     * More information on this method is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-down-method
     *
     * @return void
     */
    public function down(): void
    {
        $this->table('assinaturas')
            ->dropForeignKey(
                'plano_id'
            )
            ->dropForeignKey(
                'usuario_id'
            )->save();

        $this->table('flashcard_tags')
            ->dropForeignKey(
                'tag_id'
            )->save();

        $this->table('flashcards')
            ->dropForeignKey(
                'usuario_id'
            )->save();

        $this->table('frases_semelhantes')
            ->dropForeignKey(
                'prompt_id'
            )->save();

        $this->table('imagens_geradas')
            ->dropForeignKey(
                'prompt_id'
            )
            ->dropForeignKey(
                'traducao_id'
            )->save();

        $this->table('logs_permissoes')
            ->dropForeignKey(
                'usuario_id'
            )->save();

        $this->table('planos')
            ->dropForeignKey(
                'role_id'
            )->save();

        $this->table('preferencias_usuario')
            ->dropForeignKey(
                'usuario_id'
            )->save();

        $this->table('progresso_usuario')
            ->dropForeignKey(
                'usuario_id'
            )->save();

        $this->table('prompts')
            ->dropForeignKey(
                'usuario_id'
            )->save();

        $this->table('quizes')
            ->dropForeignKey(
                'usuario_id'
            )->save();

        $this->table('respostas_usuario')
            ->dropForeignKey(
                'usuario_id'
            )->save();

        $this->table('role_permissoes')
            ->dropForeignKey(
                'permissao_id'
            )
            ->dropForeignKey(
                'role_id'
            )->save();

        $this->table('social_profiles')
            ->dropForeignKey(
                'user_id'
            )->save();

        $this->table('tags')
            ->dropForeignKey(
                'criado_por'
            )->save();

        $this->table('traducoes')
            ->dropForeignKey(
                'prompt_id'
            )->save();

        $this->table('usuario_roles')
            ->dropForeignKey(
                'atribuido_por'
            )
            ->dropForeignKey(
                'role_id'
            )
            ->dropForeignKey(
                'usuario_id'
            )->save();

        $this->table('assinaturas')->drop()->save();
        $this->table('flashcard_tags')->drop()->save();
        $this->table('flashcards')->drop()->save();
        $this->table('frases_semelhantes')->drop()->save();
        $this->table('imagens_geradas')->drop()->save();
        $this->table('logs_permissoes')->drop()->save();
        $this->table('permissoes')->drop()->save();
        $this->table('planos')->drop()->save();
        $this->table('preferencias_usuario')->drop()->save();
        $this->table('progresso_usuario')->drop()->save();
        $this->table('prompts')->drop()->save();
        $this->table('quizes')->drop()->save();
        $this->table('respostas_usuario')->drop()->save();
        $this->table('role_permissoes')->drop()->save();
        $this->table('roles')->drop()->save();
        $this->table('social_profiles')->drop()->save();
        $this->table('tags')->drop()->save();
        $this->table('traducoes')->drop()->save();
        $this->table('users')->drop()->save();
        $this->table('usuario_roles')->drop()->save();
    }
}
