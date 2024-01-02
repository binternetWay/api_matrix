<?php
//composer update
namespace App\Models;

use App\Model;

class Agendamento extends Model{
  private $TIPO;
  private $CATALOGO;
  private $numero_clinte;
  private $SOLICITACAO_CONTEXTO;
  private $SOLICITACAO_PROBLEMA;
  private $CATEGORIA1;
  private $CATEGORIA2;
  private $CATEGORIA3;
  private $CATEGORIA4;
  private $CATEGORIA5;
  private $PROTOCOLO;
  private $CLIENTE;
  private $CLIENTE_TELEFONE;
  private $CEP;
  private $ENDERECO_COMPLEMENTO;
  private $BAIRRO;
  private $CIDADE;
  private $ESTADO;
  private $LATITUDE;
  private $LONGITUDE;
  private $DATA_ABERTURA;
  private $HORA_ABERTURA;
  private $DATA_PRAZO;
  private $HORA_PRAZO;
  private $DATA_EXECUCAO;
  private $HORA_EXECUCAO;
  private $DATA_ENCERRAMENTO_01;
  private $HORA_ENCERRAMENTO_01;
  private $REABERTURA;
  private $PRIMEIRA_DATA_AGENDA;
  private $PRIMEIRA_HORA_AGENDA;
  private $DATA_AGENDA_INICIO;
  private $HORA_AGENDA_INICIO;
  private $DATA_AGENDA_TERMINO;
  private $HORA_AGENDA_TERMINO;
  private $NOME_RESPONSAVEL_ABERTURA;
  private $SETOR_RESPONSAVEL_ABERTURA;
  private $NOME_RESPONSAVEL_AGENDADO;
  private $SETOR_RESPONSAVEL_AGENDADO;
  private $NOME_RESPONSAVEL_EXECUCAO;
  private $SETOR_RESPONSAVEL_EXECUCAO;
  private $NOME_RESPONSAVEL_FECHAMENTO;
  private $SETOR_RESPONSAVEL_FECHAMENTO;
  private $NOME_AUXILIAR;
  private $RESOLUCAO;
  private $NUMERO_CONTRATO;
  private $STATUS_CONTRATO;
  private $CONTRATO_DATA_CANCELAMENTO;
  private $CONTRATO_VENDEDOR;
  private $CONTRATO_VENDEDOR_EQUIPE;
  private $SITUACAO_ACEITE_CONTRATO;
  private $SITUACAO_EXECUCAO;
  private $STATUS_ATUAL;
  private $LANCADO_POR_NOME;
  private $LANCADO_POR_EQUIPE;
  private $VENDEDOR_ATRIBUIDO;
  private $VENDEDOR_ATRIBUIDO_EQUIPE;
  private $ponto_ordem;
  private $ponto_ordem_validado;
  private $ACOMPANHAMENTO_SAT;
  private $ACOMPANHAMENTO_SAT_DIVERGENCIA;
  private $ACOMPANHAMENTO_SAT_SUPORTE;
  private $ACOMPANHAMENTO_SAT_RESPONSAVEL;
  private $SOLICITACAO_CAMPO;
  private $CANAL_CHECKLIST;
  private $ULTIMO_RELATO;

     public function __get($atributo) {
          return $this->$atributo;
     }

	public function getInfoAgendamento($numero_contrato) {  
          $query =  
          "    SELECT * FROM 
               (SELECT DISTINCT
	               CASE WHEN (SELECT
                    REPLACE(REPLACE(REPLACE(REPLACE(reports.description,E'\r\n',' '),E'\n',' '), '  ', ' '), ';','')::VARCHAR(500)
	          FROM reports 
			     WHERE reports.assignment_id=SOLICITACOES.id
			ORDER BY reports.id DESC LIMIT 1) = 'Atividade em play' THEN 'SIM' ELSE 'NÃO' END AS Atividade_em_play,

               -- indice solicitação 
               SOLICITACOES.id,
               CASE
                    WHEN contracts.company_place_id=11 THEN 'BW'::VARCHAR(3)
                    ELSE 'WAY'::VARCHAR(3)
               END AS LOCAL,
               -- tipo da solicitação (descritivo)
               TIPOSSOLICITACOES.title AS TIPO,
               catalog_services.title AS CATALOGO,
     
          CASE 
                    WHEN TIPOSSOLICITACOES.title IN ('Venda Consultiva', 'INSTALAÇÃO', 'ATENDIMENTO INICIAL', 'CANCELAMENTO POR INADIMPLÊNCIA', 'Intermitência', 'Cancelamento', 'Desvinculo de Materiais (OS)') THEN CAST('N/A' AS VARCHAR(100))
               ELSE CAST(solicitation_classifications.title AS VARCHAR(100))
          END AS SOLICITACAO_CONTEXTO,
          CASE 
               WHEN TIPOSSOLICITACOES.title IN ('Venda Consultiva', 'INSTALAÇÃO', 'ATENDIMENTO INICIAL', 'CANCELAMENTO POR INADIMPLÊNCIA', 'Intermitência', 'Cancelamento', 'Desvinculo de Materiais (OS)') THEN CAST('N/A' AS VARCHAR(100))
               ELSE CAST(solicitation_problems.title AS VARCHAR(100))
          END AS SOLICITACAO_PROBLEMA,
               -- CATEGORIZAÇÃO DA ARVORE DE CHEGA A SOLICITAÇÃO
               MAT_CAT1.title AS CATEGORIA1,
               MAT_CAT2.title AS CATEGORIA2,
               MAT_CAT3.title AS CATEGORIA3,
               MAT_CAT4.title AS CATEGORIA4,
               MAT_CAT5.title AS CATEGORIA5,
               -- PROTOCOLO INDICADO NA SOLICITAÇÃO
               SOLICITACOES_INCIDENTES.protocol AS PROTOCOLO,
               -- CLIENTE SOLICITANTE E SEUS DADOS DE ENDEREÇO
               CLIENTES.name AS CLIENTE,
               CLIENTES.phone AS CLIENTE_TELEFONE,
               CLIENTES.cell_phone_1 AS CLIENTE_CELULAR,
               CLIENTES.postal_code AS CEP, 
               CAST(CONCAT(CLIENTES.street_type, ' ', CLIENTES.street, ' ', CLIENTES.NUMBER) AS VARCHAR(200)) AS ENDERECO,
               CLIENTES.address_complement AS ENDERECO_COMPLEMENTO, 
               CLIENTES.neighborhood AS BAIRRO, 
               CLIENTES.city AS CIDADE, 
               CLIENTES.state AS ESTADO,
               CLIENTES.lat AS LATITUDE,
               CLIENTES.lng AS LONGITUDE,
               -- DATA DE ABERTURA DA SOLICITAÇÃO/CRIAÇÃO
               CAST(TO_CHAR( SOLICITACOES.beginning_date, 'DD/MM/YYYY') AS VARCHAR(10))  AS DATA_ABERTURA,
               CAST(TO_CHAR( SOLICITACOES.beginning_date, 'HH24:MI:SS') AS VARCHAR(10))  AS HORA_ABERTURA,
               -- DATA DE PRAZO DO SLA DA SOLICITAÇÃO
               CAST(TO_CHAR(  SOLICITACOES.final_date, 'DD/MM/YYYY') AS VARCHAR(10)) AS DATA_PRAZO,
               CAST(TO_CHAR(  SOLICITACOES.final_date, 'HH24:MI:SS') AS VARCHAR(10)) AS HORA_PRAZO,
               -- EXECUCAÇÃO/CONCLUSÃO DA SOLICITAÇÃO
               CAST(TO_CHAR(  SOLICITACOES.report_closing_date, 'DD/MM/YYYY') AS VARCHAR(10)) AS DATA_EXECUCAO,
               CAST(TO_CHAR(  SOLICITACOES.report_closing_date, 'HH24:MI:SS') AS VARCHAR(10)) AS HORA_EXECUCAO,
               -- ENCERRAMENTO COMPLETO DA SOLICITAÇÃO CASO TIVER OCORRIDO MAIS DE UM ENCERRAMENTO, APONTA O PRIMEIRO DA SOLICITAÇÃO.
               CAST(TO_CHAR(  RESPONSAVELENCERRAMENTO.final_date, 'DD/MM/YYYY') AS VARCHAR(10)) AS DATA_ENCERRAMENTO_01,
               CAST(TO_CHAR(  RESPONSAVELENCERRAMENTO.final_date, 'HH24:MI:SS') AS VARCHAR(10)) AS HORA_ENCERRAMENTO_01,
               -- ENCERRAMENTO COMPLETO DA SOLICITAÇÃO (EM CASO DE REABERTURA OU MANUTENÇÃO NA ORDEM APRESENTARÁ DUAS DATAS DIVERGENTES PARA COMPARATIVO
               CAST(TO_CHAR( SOLICITACOES.conclusion_date, 'DD/MM/YYYY') AS VARCHAR(10)) AS DATA_ENCERRAMENTO_FINAL,
               CAST(TO_CHAR( SOLICITACOES.conclusion_date, 'HH24:MI:SS') AS VARCHAR(10)) AS HORA_ENCERRAMENTO_FINAL,
               --------------------------------------------------------------------------------------
               --- IF (SOLICITACOES.conclusion_date IS NULL, 'ANDAMENTO', if(STATUSSOLICITACAO.title='Cancelado','CANCELADA', IF(SOLICITACOES.conclusion_date-RESPONSAVELENCERRAMENTO.final_date > :XTOLERANCIA, 'REABERTA', 'CONCLUIDA'))) AS REABERTURA,
               CASE 
                    WHEN SOLICITACOES.conclusion_date IS NULL THEN CAST('ANDAMENTO' AS VARCHAR(30)) 
                    WHEN STATUSSOLICITACAO.title='Cancelado' THEN CAST('CANCELADA'AS VARCHAR(30)) 
                    WHEN DATEDIFF(SOLICITACOES.conclusion_date, RESPONSAVELENCERRAMENTO.final_date) > 60 THEN CAST('REABERTA' AS VARCHAR(30))
                              -- WHEN DATEDIFF(SOLICITACOES.conclusion_date, RESPONSAVELENCERRAMENTO.final_date) > #XTOLERANCIA THEN CAST('REABERTA' AS VARCHAR(30))
                    ELSE CAST('CONCLUIDA' AS VARCHAR(30)) 
               END AS REABERTURA,
               -- Tempo trabalhado na abertura da solicitação
               (
                    SELECT
                         SEC_TO_TIME(SUM(seconds_worked))
                    FROM reports 
                    WHERE assignment_id=SOLICITACOES.id AND reports.created_by=SOLICITACOES.created_by
               ) AS TEMPO_TRABALHADO_ABERTURA,
               -- TEMPO TRABALHADO (APENAS ULTIMO EXECUTANTE CONTA, SE OCORREU REENCAMINHAMENTOS NÃO APRESENTA)
               SEC_TO_TIME(REP_TEMPO.TEMPO) AS TEMPO_TRABALHADO,
               -- DATA AGENDADA DE SERVIÇO
               CAST(TO_CHAR( PRIMEIRAAGENDA.DATAAGENDA, 'DD/MM/YYYY') AS VARCHAR(10))  AS PRIMEIRA_DATA_AGENDA,
               CAST(TO_CHAR( PRIMEIRAAGENDA.DATAAGENDA, 'HH24:MI:SS') AS VARCHAR(10))  AS PRIMEIRA_HORA_AGENDA,
               CAST(TO_CHAR( AGENDATECNICA.start_date, 'DD/MM/YYYY') AS VARCHAR(10))  AS DATA_AGENDA_INICIO,
               CAST(TO_CHAR( AGENDATECNICA.start_date, 'HH24:MI:SS') AS VARCHAR(10))  AS HORA_AGENDA_INICIO,
               CAST(TO_CHAR( AGENDATECNICA.end_date, 'DD/MM/YYYY') AS VARCHAR(10))  AS DATA_AGENDA_TERMINO,
               CAST(TO_CHAR( AGENDATECNICA.end_date, 'HH24:MI:SS') AS VARCHAR(10))  AS HORA_AGENDA_TERMINO,
     
               -- RESPONSAVEL DA ABERTURA
               RESPONSAVELABERTURA.name AS NOME_RESPONSAVEL_ABERTURA,
               -- SETOR DO RESPONSÁVEL PELA ABERTURA
               RESPONSAVELABERTURASETOR.title AS SETOR_RESPONSAVEL_ABERTURA,
               -- RESPONSAVEL AGENDADO PARA EXECUTAR
               AGENDATECNICARESPONSAVEL.name AS NOME_RESPONSAVEL_AGENDADO,
               -- SETOR RESPONSAVEL A EXECUTAR
               RESPONSAVELAGENDAMENTOSETOR.title AS SETOR_RESPONSAVEL_AGENDADO,
               -- CASO NAO ESTEJA CANCELADO VERIFICA O RESPONSAVEL PELA EXECUCAO DA SOLICITAÇÃO
               CASE
                    WHEN STATUSSOLICITACAO.title='Cancelado' THEN CAST('CANCELADA' AS VARCHAR(100)) 
                    WHEN RESPONSAVELEXECUCAO.name IS NULL AND (STATUSSOLICITACAO.title = 'Encerramento') THEN CAST(RESPONSAVELABERTURA.name AS VARCHAR(100)) 
                    ELSE CAST(RESPONSAVELEXECUCAO.name AS VARCHAR(100)) 
               END AS NOME_RESPONSAVEL_EXECUCAO,
               -- SETOR RESPONSAVEL EXECUCAO
               CASE
                    WHEN STATUSSOLICITACAO.title='Cancelado' THEN CAST('CANCELADA' AS VARCHAR(100)) 
                    WHEN RESPONSAVELEXECUCAOSETOR.title IS NULL AND (STATUSSOLICITACAO.title = 'Encerramento') THEN CAST(RESPONSAVELABERTURASETOR.title AS VARCHAR(100)) 
                    ELSE CAST(RESPONSAVELEXECUCAOSETOR.title AS VARCHAR(100)) 
               END AS SETOR_RESPONSAVEL_EXECUCAO,
               -- CASO NAO ESTAJA CANCELADO O RESPONSAVEL DO ENCERRAMENTO, SE QUEM FECHOU TAMBEM ENCERROU JA APRESENTA TAMBEM
               CASE
                    WHEN STATUSSOLICITACAO.title <> 'Encerramento' THEN CAST('N/A' AS VARCHAR(100))  -- upper(STATUSSOLICITACAO.title)
                    WHEN NOMERESPONSAVELENCERRAMENTO.name IS NULL THEN CAST(RESPONSAVELABERTURA.name AS VARCHAR(100)) 
                    ELSE CAST(NOMERESPONSAVELENCERRAMENTO.name AS VARCHAR(100)) 
               END::VARCHAR(100) AS NOME_RESPONSAVEL_FECHAMENTO,
               -- SETOR RESPONSAVEL ENCERRAMENTO
               CASE
                    WHEN STATUSSOLICITACAO.title <> 'Encerramento' THEN CAST('N/A' AS VARCHAR(100))  --- upper(STATUSSOLICITACAO.title)
                    WHEN RESPONSAVELENCERRAMENTOSETOR.title IS NULL THEN CAST(RESPONSAVELABERTURASETOR.title AS VARCHAR(100)) 
                    ELSE CAST(RESPONSAVELENCERRAMENTOSETOR.title AS VARCHAR(100)) 
               END AS SETOR_RESPONSAVEL_FECHAMENTO,
                    -- SE EXISTIR UM AUXILIAR NA ORDEM, RELATA-SE TAMBEM
                    -- CAST(AUXILIARTECNICO.NOMEAUXILIAR AS CHAR(1000) CHARACTER SET utf8) AS NOME_AUXILIAR,
                    CAST(AUXILIARTECNICO.NOMEAUXILIAR AS VARCHAR(100))  AS NOME_AUXILIAR,
               -- CASO EXISTA UM RESPONSÁVEL ENCAMINHADO, SOLICITAÇÃO NÃO FOI SOLUCIONADA LOCALMENTE 
               CASE
                    WHEN RESPONSAVELABERTURA.name IS NULL THEN CAST('AUTOSERVICO' AS VARCHAR(100)) 
                    WHEN RESPONSAVELEXECUCAO.name IS NULL AND NOMERESPONSAVELENCERRAMENTO.name IS NULL AND STATUSSOLICITACAO.title NOT IN ('Encerramento', 'Cancelado') THEN CAST('PENDENTE_EXECUCAO' AS VARCHAR(100)) 
                    WHEN RESPONSAVELEXECUCAO.name IS NULL AND NOMERESPONSAVELENCERRAMENTO.name IS NULL AND STATUSSOLICITACAO.title IN ('Encerramento', 'Cancelado') THEN CAST('LOCAL' AS VARCHAR(100)) 
                    WHEN RESPONSAVELABERTURA.name=NOMERESPONSAVELENCERRAMENTO.name THEN CAST('LOCAL' AS VARCHAR(100)) 
                    WHEN RESPONSAVELABERTURA.name<>NOMERESPONSAVELENCERRAMENTO.name AND NOMERESPONSAVELENCERRAMENTO.name IS NOT NULL THEN CAST('ENCAMINHADO' AS VARCHAR(100)) 
                    WHEN RESPONSAVELABERTURA.name<>RESPONSAVELEXECUCAO.name AND RESPONSAVELEXECUCAO.name IS NOT NULL THEN CAST('ENCAMINHADO' AS VARCHAR(100)) 
                    ELSE CAST('MAPEAR' AS VARCHAR(100))
               END AS RESOLUCAO,
               -- SITUAÇÃO DO CONTRATO, POR TIPO (0 FISICO, 1 REJEITADO, 2 ACEITO, 3 AGUARDANDO, 4 SEM ACEITE ENVIADO)
               CASE
                    WHEN contracts.contract_number IS NULL THEN CAST('NAO SE APLICA' AS VARCHAR(100))
                    ELSE CAST(contracts.contract_number AS VARCHAR(100)) 
               END AS NUMERO_CONTRATO,
               CASE
                    WHEN contracts.v_status IS NULL THEN CAST('NAO SE APLICA' AS VARCHAR(100)) 
                         ELSE CAST(contracts.v_status AS VARCHAR(100)) 
               END AS STATUS_CONTRATO,
                    
               CASE
                    WHEN contracts.cancellation_date IS NULL THEN CAST('NAO CANCELADO' AS VARCHAR(15)) 
                         ELSE CAST(contracts.cancellation_date AS VARCHAR(15))
               END AS CONTRATO_DATA_CANCELAMENTO,
          
               CAST('' AS VARCHAR(10))  AS CONTRATO_VENDEDOR,
               CAST('' AS VARCHAR(10))  AS CONTRATO_VENDEDOR_EQUIPE,
               (CASE contracts.client_acceptance
                    WHEN 0 THEN CAST('FISICO' AS VARCHAR(100)) 
                    WHEN 1 THEN CAST('REJEITADO PELO CLIENTE' AS VARCHAR(100)) 
                    WHEN 2 THEN CAST('ACEITO' AS VARCHAR(100)) 
                    WHEN 3 THEN CAST('AGUARDANDO ACEITE PELO CLIENTE' AS VARCHAR(100)) 
                    WHEN 4 THEN CAST('SEM ACEITE ENVIADO' AS VARCHAR(100)) 
                    ELSE CAST('NAO SE APLICA' AS VARCHAR(100)) 
                    END ) AS SITUACAO_ACEITE_CONTRATO,
               -- SITUAÇÃO DO DA SOLICITAÇÃOP ATUAL
               CASE
                    WHEN STATUSSOLICITACAO.title='Cancelado' THEN CAST('CANCELADA' AS VARCHAR(100))
                    WHEN SOLICITACOES.report_closing_date IS NULL AND SOLICITACOES.final_date < NOW() THEN CAST('ATRASADA' AS VARCHAR(100))
                    WHEN SOLICITACOES.report_closing_date IS NULL THEN CAST('AGUARDANDO' AS VARCHAR(100))
                    WHEN SOLICITACOES.final_date<SOLICITACOES.report_closing_date THEN CAST('REALIZADA APOS PRAZO' AS VARCHAR(100))
                    ELSE CAST('REALIZADA NO PRAZO' AS VARCHAR(100))
               END AS SITUACAO_EXECUCAO,
               -- STATUS (COMPLEMENTACAO BASICA DA SITUAÇÃO)
               CAST(UPPER(STATUSSOLICITACAO.title) AS VARCHAR(100)) AS STATUS_ATUAL,
               LANCADOR_USUARIOS.name AS LANCADO_POR_NOME,
               LANCADOR_EQUIPES.title AS LANCADO_POR_EQUIPE,
               VENDEDORES.name AS VENDEDOR_ATRIBUIDO,
               CASE
                    WHEN SUBSTRING(VENDEDORES.name, 1,4)='RV -' THEN CAST('Revendedores' AS VARCHAR(100))
                         ELSE CAST(VENDEDORES_EQUIPES.title AS VARCHAR(100))
               END AS VENDEDOR_ATRIBUIDO_EQUIPE,
               CASE
                    WHEN RESPONSAVELEXECUCAOSETOR.title = 'PLANEJAMENTO TÉCNICO' AND FIBRA_UTILIZADA.units IS NULL THEN 0.5
                    WHEN RESPONSAVELEXECUCAOSETOR.title = 'PLANEJAMENTO TÉCNICO' AND FIBRA_UTILIZADA.units < 250 THEN 1.0
                    WHEN RESPONSAVELEXECUCAOSETOR.title = 'PLANEJAMENTO TÉCNICO' AND FIBRA_UTILIZADA.units BETWEEN 250 AND 500 THEN 2.0
                    WHEN RESPONSAVELEXECUCAOSETOR.title = 'PLANEJAMENTO TÉCNICO' AND FIBRA_UTILIZADA.units BETWEEN 500 AND 750 THEN 3.0
                    ELSE 0
               END AS ponto_ordem,
     
               CASE
                    WHEN FIBRA_UTILIZADA.units IS NULL THEN 0.5
                    WHEN FIBRA_UTILIZADA.units < 250 THEN 1.0 
                    WHEN FIBRA_UTILIZADA.units BETWEEN 250 AND 500 THEN 2.0
                    WHEN FIBRA_UTILIZADA.units BETWEEN 500 AND 750 THEN 3.0
               END AS FIBRA_PONTOS,
               FIBRA_UTILIZADA.units AS FIBRA_METRAGEM,
               CASE
                    WHEN REP_N2.description IS NULL AND REP_N2_DIV.description IS NULL THEN 0
                    WHEN RESPONSAVELEXECUCAOSETOR.title = 'PLANEJAMENTO TÉCNICO' AND FIBRA_UTILIZADA.units IS NULL THEN 0.5
                    WHEN RESPONSAVELEXECUCAOSETOR.title = 'PLANEJAMENTO TÉCNICO' AND FIBRA_UTILIZADA.units < 250 THEN 1.0
                    WHEN RESPONSAVELEXECUCAOSETOR.title = 'PLANEJAMENTO TÉCNICO' AND FIBRA_UTILIZADA.units BETWEEN 250 AND 500 THEN 2.0
                    WHEN RESPONSAVELEXECUCAOSETOR.title = 'PLANEJAMENTO TÉCNICO' AND FIBRA_UTILIZADA.units BETWEEN 500 AND 750 THEN 3.0
                    ELSE 0
               END AS ponto_ordem_validado,
               CASE 
                         WHEN REP_N2.description IS NULL AND REP_N2_DIV.description IS NULL THEN 'NAO'
                         ELSE 'SIM'
               END::VARCHAR(3) AS ACOMPANHAMENTO_SAT,
               CASE 
                         WHEN REP_N2_DIV.description IS NULL THEN 'NAO'
                         ELSE 'SIM'
               END::VARCHAR(3) AS ACOMPANHAMENTO_SAT_DIVERGENCIA,
               CASE 
                         WHEN REP_N2_SUP.description IS NULL THEN 'NAO'
                         ELSE 'SIM'
               END::VARCHAR(3) AS ACOMPANHAMENTO_SAT_SUPORTE,
               REP_N2_RESP_USER.name AS ACOMPANHAMENTO_SAT_RESPONSAVEL,
               CASE
                    WHEN TIPOSSOLICITACOES.code LIKE 'LG%' THEN 'SIM'
                    ELSE 'NAO'
               END::VARCHAR(3) AS SOLICITACAO_CAMPO,
               REP_AGENDA.QTDA_AGENDA,
               -- CHECKLIST DE VOZ
               CASE
                    WHEN SOLICITACOES_INCIDENTES.beginning_checklist IS NULL OR SOLICITACOES_INCIDENTES.beginning_checklist = '' THEN 'N/A'
                    ELSE
                         CASE
                              WHEN ((SOLICITACOES_INCIDENTES.beginning_checklist::json->'0_'->>'label')::VARCHAR(20) LIKE '%VOZ%' AND (SOLICITACOES_INCIDENTES.beginning_checklist::json->'0_'->>'value')::VARCHAR(1) = '1') THEN 'VOZ'
                              WHEN ((SOLICITACOES_INCIDENTES.beginning_checklist::json->'1_'->>'label')::VARCHAR(20) LIKE '%VOZ%' AND (SOLICITACOES_INCIDENTES.beginning_checklist::json->'1_'->>'value')::VARCHAR(1) = '1') THEN 'VOZ'
                              WHEN ((SOLICITACOES_INCIDENTES.beginning_checklist::json->'2_'->>'label')::VARCHAR(20) LIKE '%VOZ%' AND (SOLICITACOES_INCIDENTES.beginning_checklist::json->'2_'->>'value')::VARCHAR(1) = '1') THEN 'VOZ'
                              
                              WHEN ((SOLICITACOES_INCIDENTES.beginning_checklist::json->'0_'->>'label')::VARCHAR(20) LIKE '%WHATS%' AND (SOLICITACOES_INCIDENTES.beginning_checklist::json->'0_'->>'value')::VARCHAR(1) = '1') THEN 'WHATS'
                              WHEN ((SOLICITACOES_INCIDENTES.beginning_checklist::json->'1_'->>'label')::VARCHAR(20) LIKE '%WHATS%' AND (SOLICITACOES_INCIDENTES.beginning_checklist::json->'1_'->>'value')::VARCHAR(1) = '1') THEN 'WHATS'
                              WHEN ((SOLICITACOES_INCIDENTES.beginning_checklist::json->'2_'->>'label')::VARCHAR(20) LIKE '%WHATS%' AND (SOLICITACOES_INCIDENTES.beginning_checklist::json->'2_'->>'value')::VARCHAR(1) = '1') THEN 'WHATS'
               
                              WHEN ((SOLICITACOES_INCIDENTES.beginning_checklist::json->'0_'->>'label')::VARCHAR(20) LIKE '%LOJA%' AND (SOLICITACOES_INCIDENTES.beginning_checklist::json->'0_'->>'value')::VARCHAR(1) = '1') THEN 'LOJA'
                              WHEN ((SOLICITACOES_INCIDENTES.beginning_checklist::json->'1_'->>'label')::VARCHAR(20) LIKE '%LOJA%' AND (SOLICITACOES_INCIDENTES.beginning_checklist::json->'1_'->>'value')::VARCHAR(1) = '1') THEN 'LOJA'
                              WHEN ((SOLICITACOES_INCIDENTES.beginning_checklist::json->'2_'->>'label')::VARCHAR(20) LIKE '%LOJA%' AND (SOLICITACOES_INCIDENTES.beginning_checklist::json->'2_'->>'value')::VARCHAR(1) = '1') THEN 'LOJA'
                              
                              ELSE 'INDEFINIDO'
                         END
               END::VARCHAR(20) AS CANAL_CHECKLIST,
     
               (
                    SELECT
                         --reports.description::VARCHAR(500)
                         REPLACE(REPLACE(reports.description::VARCHAR(500), E'\\n', ''), E'\n', '')
                         FROM reports 
                         WHERE reports.assignment_id = SOLICITACOES.id
                         ORDER BY reports.id DESC LIMIT 1
               ) AS ULTIMO_RELATO
     
               FROM assignment_incidents AS SOLICITACOES_INCIDENTES  
               LEFT JOIN assignments AS SOLICITACOES
                    ON SOLICITACOES.id=SOLICITACOES_INCIDENTES.assignment_id
               -- TIPO DE SOLICITAÇÃO
               LEFT JOIN incident_types AS TIPOSSOLICITACOES
                    ON TIPOSSOLICITACOES.id=SOLICITACOES_INCIDENTES.incident_type_id
               -- STATUS DA SOLICITAÇÃO
               LEFT JOIN incident_status AS STATUSSOLICITACAO
                    ON STATUSSOLICITACAO.id=SOLICITACOES_INCIDENTES.incident_status_id
               -- CLIENTE DA RESPECTIVA ORDEM
               LEFT JOIN people AS CLIENTES
                    ON SOLICITACOES_INCIDENTES.client_id=CLIENTES.id
               -- USUARIO DA ABERTURA DA ORDEM
               LEFT JOIN users AS RESPONSAVELABERTURA
                    ON SOLICITACOES.created_by=RESPONSAVELABERTURA.id
               -- SETOR DO USUARIO DA ABERTURA DA ORDEM
               LEFT JOIN teams AS RESPONSAVELABERTURASETOR
                    ON RESPONSAVELABERTURA.team_id=RESPONSAVELABERTURASETOR.id
               -- QUANDO EXISTIR EXECUÇÃO, QUAL O RESPONSÁVEL PELA MESMA.
               LEFT JOIN (SELECT DISTINCT(reports.assignment_id) AS assignmentID, 
                                        MIN(reports.person_id) AS person_id
                                        FROM reports 
                                        WHERE reports.person_id IN (SELECT people.id FROM people WHERE people.technical=TRUE) 
                                        GROUP BY reports.assignment_id) AS RELATOSTECNICOS
                                                  ON RELATOSTECNICOS.assignmentID=SOLICITACOES_INCIDENTES.assignment_id
               LEFT JOIN people AS RESPONSAVELEXECUCAO
                    ON RELATOSTECNICOS.person_id=RESPONSAVELEXECUCAO.id
               -- SETOR DO USUARIO DA EXECUÇÃO DA ORDEM QUANDO EXISTIR
               LEFT JOIN users AS RESPONSAVELEXECUCAOUSUARIO
                    ON RESPONSAVELEXECUCAOUSUARIO.tx_id=RESPONSAVELEXECUCAO.tx_id
               -- SETOR DO USUARIO DA EXECUCAO DA ORDEM
               LEFT JOIN teams AS RESPONSAVELEXECUCAOSETOR
                    ON RESPONSAVELEXECUCAOUSUARIO.team_id=RESPONSAVELEXECUCAOSETOR.id
               -- CASO NAO EXISTA EXECUÇÃO, QUAL O RESPONSAVEL AO QUAL FOI ENCAMINHADO
               LEFT JOIN (SELECT * FROM 
                    (
                         SELECT RESPENC1.* 
                         FROM (SELECT * from assignment_person_routings where assignment_person_routings.destination_person_id IN (SELECT people.id FROM people WHERE people.technical='1')) AS RESPENC1 
                         LEFT JOIN (SELECT * from assignment_person_routings where assignment_person_routings.destination_person_id IN (SELECT people.id FROM people WHERE people.technical='1')) AS RESPENC2 
                              ON (RESPENC1.assignment_id = RESPENC2.assignment_id AND RESPENC1.id < RESPENC2.id) 
                         WHERE RESPENC2.id IS NULL) AS RESPENC) AS RESPONSAVELENCAMINHADO
                    ON RESPONSAVELENCAMINHADO.assignment_id=SOLICITACOES_INCIDENTES.assignment_id
               LEFT JOIN people AS NOMERESPONSAVELENCAMINHADO
                    ON RESPONSAVELENCAMINHADO.destination_person_id=NOMERESPONSAVELENCAMINHADO.id
               -- SETOR DO USUARIO ENCAMINHADO DA ORDEM QUANDO EXISTIR
               LEFT JOIN users AS RESPONSAVELENCAMINHADOUSUARIO
                    ON RESPONSAVELENCAMINHADOUSUARIO.tx_id=NOMERESPONSAVELENCAMINHADO.tx_id
               -- SETOR DO USUARIO DA ENCAMINHADO DA ORDEM
               LEFT JOIN teams AS RESPONSAVELENCAMINHADOSETOR
                    ON RESPONSAVELENCAMINHADOUSUARIO.team_id=RESPONSAVELENCAMINHADOSETOR.id
               -- CASO NAO EXISTA EXECUÇÃO, QUAL O RESPONSAVEL AO QUAL FOI ENCAMINHADO, SE SITUAÇÃO EM ABERTO/ANDAMENTO RETORNAR O ULTIMO RESPONSÁVEL PELA ORDEM
               LEFT JOIN (SELECT * FROM 
               (SELECT RESPAND1.* 
                         FROM assignment_person_routings AS RESPAND1 
                         LEFT JOIN assignment_person_routings AS RESPAND2 
                              ON (RESPAND1.assignment_id = RESPAND2.assignment_id AND RESPAND1.id < RESPAND2.id) 
                         WHERE RESPAND2.id IS NULL) AS RESPAND) AS RESPONSAVELANDAMENTO
                    ON RESPONSAVELANDAMENTO.assignment_id=SOLICITACOES_INCIDENTES.assignment_id
               LEFT JOIN people AS NOMERESPONSAVELANDAMENTO
                    ON RESPONSAVELANDAMENTO.destination_person_id=NOMERESPONSAVELANDAMENTO.id
               -- SE ORDEM ENCERRADA (NÃO APENAS FECHADA), APARECERÁ O NOME DO RESPONSÁVEL DO ENCERRAMENTO
               LEFT JOIN (SELECT * FROM 
               (SELECT RESPENCERRA1.* 
                    FROM (SELECT id, person_id, assignment_id, final_date FROM reports WHERE progress=100) AS RESPENCERRA1 
                    LEFT JOIN (SELECT id, person_id, assignment_id, final_date FROM reports WHERE progress=100) AS RESPENCERRA2 
                         ON (RESPENCERRA1.assignment_id = RESPENCERRA2.assignment_id AND RESPENCERRA1.id > RESPENCERRA2.id) 
                    WHERE RESPENCERRA2.id IS NULL) AS RESPENCERRA) AS RESPONSAVELENCERRAMENTO
                    ON RESPONSAVELENCERRAMENTO.assignment_id=SOLICITACOES_INCIDENTES.assignment_id
               LEFT JOIN people AS NOMERESPONSAVELENCERRAMENTO
                    ON RESPONSAVELENCERRAMENTO.person_id=NOMERESPONSAVELENCERRAMENTO.id
               -- SETOR DO USUARIO DO ENCERRAMENTO DA ORDEM QUANDO EXISTIR
               LEFT JOIN users AS RESPONSAVELENCERRAMENTOUSUARIO
                    ON RESPONSAVELENCERRAMENTOUSUARIO.tx_id=NOMERESPONSAVELENCERRAMENTO.tx_id
               -- SETOR DO USUARIO DO ENCERRAMENTO DA ORDEM
               LEFT JOIN teams AS RESPONSAVELENCERRAMENTOSETOR
                    ON RESPONSAVELENCERRAMENTOUSUARIO.team_id=RESPONSAVELENCERRAMENTOSETOR.id
               -- AUXILIAR DA ORDEM
               LEFT JOIN (SELECT assignments.id, assignments.root_id FROM assignments WHERE assignment_type='RAUX') AS SOLICITACOES_AUXILIARES
                    ON SOLICITACOES_AUXILIARES.root_id=SOLICITACOES.id
               LEFT JOIN (SELECT assignment_id, SUBSTRING_INDEX(description,'>', -1) AS NOMEAUXILIAR from reports WHERE title LIKE 'Auxil%') AS AUXILIARTECNICO
                    ON AUXILIARTECNICO.assignment_id=SOLICITACOES_AUXILIARES.id
               
               -- CASE EXISTA AGENDAMENTO, APONTA O PRIMEIRO AGENDAMENTO REGISTRADO NA SOLICITAÇÃO
               LEFT JOIN (SELECT DISTINCT(schedules.assignment_id) AS assignmentID,
                    schedules.start_date AS DATAAGENDA
                                   FROM schedules   LIMIT 1   
                    ) AS PRIMEIRAAGENDA
                                        ON PRIMEIRAAGENDA.assignmentID=SOLICITACOES_INCIDENTES.assignment_id		 
               -- CASO EXISTA AGENDAMENTO, APONTA O ULTIMO AGENDAMENTO REGISTRADO A SOLICITAÇÃO
               LEFT JOIN ((SELECT AGENDA1.* FROM (SELECT * FROM schedules) AS AGENDA1
                         LEFT JOIN (SELECT * FROM schedules) AS AGENDA2 ON (AGENDA1.assignment_id=AGENDA2.assignment_id AND AGENDA1.id < AGENDA2.id)
                         WHERE AGENDA2.id IS NULL)) AS AGENDATECNICA
                    ON AGENDATECNICA.assignment_id = SOLICITACOES_INCIDENTES.assignment_id
                         
               LEFT JOIN people AS AGENDATECNICARESPONSAVEL
               ON AGENDATECNICA.person_id=AGENDATECNICARESPONSAVEL.id
               -- SETOR DO USUARIO DO AGENDAMENTO DA ORDEM QUANDO EXISTIR
               LEFT JOIN users AS RESPONSAVELAGENDAMENTOUSUARIO
                    ON RESPONSAVELAGENDAMENTOUSUARIO.tx_id=AGENDATECNICARESPONSAVEL.tx_id
               -- SETOR DO USUARIO DO AGENDAMENTO DA ORDEM
               LEFT JOIN teams AS RESPONSAVELAGENDAMENTOSETOR
                    ON RESPONSAVELAGENDAMENTOUSUARIO.team_id=RESPONSAVELAGENDAMENTOSETOR.id
               -- ITEM DE CONTRATO ASSOCIADO A ETIQUETA ESCOLHIDA NA SOLICITAÇÃO
               LEFT JOIN (SELECT * FROM contract_items) AS CONTRATO_ITENS
                    ON SOLICITACOES_INCIDENTES.contract_service_tag_id=CONTRATO_ITENS.contract_service_tag_id
               -- CONTRATO PAI, ASSOCIADO AO ITEM DA ETIQUETA SELECIONADA
               LEFT JOIN contracts
               ON CONTRATO_ITENS.contract_id=contracts.id
               -- JUNÇÃO A TABELA PESSOAS PARA DADOS DO VENDEDOR ATRIBUIDO
               LEFT JOIN
               people AS VENDEDORES
               ON contracts.seller_1_id=VENDEDORES.id
               -- JUNÇÃO DA TABELA PESSOAS PARA USUARIO DO RESPECTIVO VENDEDOR ATRIBUIDO
               LEFT JOIN
               users AS VENDEDORES_USUARIOS
               ON VENDEDORES_USUARIOS.tx_id=VENDEDORES.tx_id
               -- JUNÇÃO DA TABELA DO USUARIO DO VENDEDOR ATRIBUIDO PARA EQUIPE RESPECTIVA
               LEFT JOIN 
               teams AS VENDEDORES_EQUIPES
               ON VENDEDORES_USUARIOS.team_id=VENDEDORES_EQUIPES.id
               -- JUNÇÃO DA TABELA PESSOAS PARA USUARIO DO RESPECTIVO LANÇADOR ATRIBUIDO
               LEFT JOIN
               users AS LANCADOR_USUARIOS
               ON LANCADOR_USUARIOS.id=contracts.created_by
               -- JUNÇÃO DA TABELA DO USUARIO DO LANÇADOR ATRIBUIDO PARA EQUIPE RESPECTIVA
               LEFT JOIN 
               teams AS LANCADOR_EQUIPES
               ON LANCADOR_USUARIOS.team_id=LANCADOR_EQUIPES.id
               -- JUNÇÃO DA TABELA DE RELATOS PARA CALCULO DE TEMPO TRABALHADO
               LEFT JOIN
               (
                    SELECT
                    assignment_id,
                    SUM(seconds_worked) AS TEMPO
                    FROM reports AS REP_TRAB 
                    GROUP BY assignment_id
               ) AS REP_TEMPO
               ON REP_TEMPO.assignment_id=SOLICITACOES.id
               LEFT JOIN solicitation_classifications
                    ON SOLICITACOES_INCIDENTES.solicitation_classification_id=solicitation_classifications.id
               LEFT JOIN solicitation_problems
                    ON SOLICITACOES_INCIDENTES.solicitation_problem_id=solicitation_problems.id
               LEFT JOIN catalog_services
                    ON catalog_services.id=SOLICITACOES_INCIDENTES.catalog_service_id
               LEFT JOIN 
               (SELECT 
                         person_product_movimentations.assignment_id,  
                              SUM(person_product_movimentations.units) AS units
                              -- * 
               FROM person_product_movimentations
               WHERE service_product_id=79
               GROUP BY person_product_movimentations.assignment_id ) AS FIBRA_UTILIZADA
                    ON FIBRA_UTILIZADA.assignment_id = SOLICITACOES.id 
               LEFT JOIN solicitation_category_matrices
                         ON SOLICITACOES_INCIDENTES.solicitation_category_matrix_id=solicitation_category_matrices.id
               LEFT JOIN solicitation_service_categories AS MAT_CAT1
                         ON MAT_CAT1.id=solicitation_category_matrices.service_category_id_1
               LEFT JOIN solicitation_service_categories AS MAT_CAT2
                         ON MAT_CAT2.id=solicitation_category_matrices.service_category_id_2
               LEFT JOIN solicitation_service_categories AS MAT_CAT3
                         ON MAT_CAT3.id=solicitation_category_matrices.service_category_id_3
               LEFT JOIN solicitation_service_categories AS MAT_CAT4
                         ON MAT_CAT4.id=solicitation_category_matrices.service_category_id_4
               LEFT JOIN solicitation_service_categories AS MAT_CAT5
                         ON MAT_CAT5.id=solicitation_category_matrices.service_category_id_5
               LEFT JOIN reports AS REP_N2
                         ON REP_N2.assignment_id=SOLICITACOES.id AND (UPPER(REP_N2.description) LIKE '%SOLICITAÇÃO ACOMPANHADO POR EQUIPE N2%' 
                                                                      OR UPPER(REP_N2.description) LIKE '%SOLICITAÇÃO ACOMPANHADA POR EQUIPE SAT%')
               LEFT JOIN reports AS REP_N2_DIV
                         ON REP_N2_DIV.assignment_id=SOLICITACOES.id AND (UPPER(REP_N2_DIV.description) LIKE '%ACOMPANHAMENTO N2 - APONTAMENTO DE DIVERGENCIAS%'
                                                                      OR UPPER(REP_N2_DIV.description) LIKE '%ACOMPANHAMENTO SAT - APONTAMENTO DE DIVERGENCIAS%')
               LEFT JOIN reports AS REP_N2_SUP
                         ON REP_N2_SUP.assignment_id=SOLICITACOES.id AND (UPPER(REP_N2_SUP.description) LIKE '%ACOMPANHAMENTO N2 COM SUPORTE%'
                                                                      OR UPPER(REP_N2_SUP.description) LIKE '%ACOMPANHAMENTO SAT COM SUPORTE%')
               LEFT JOIN reports AS REP_N2_RESP
                    ON REP_N2_RESP.assignment_id=SOLICITACOES.id AND (UPPER(REP_N2_RESP.description) LIKE '%SOLICITAÇÃO ACOMPANHADO POR EQUIPE N2%' OR UPPER(REP_N2_RESP.description) LIKE '%SOLICITAÇÃO ACOMPANHADA POR EQUIPE SAT%' OR
                                                                           UPPER(REP_N2_RESP.description) LIKE '%ACOMPANHAMENTO N2 - APONTAMENTO DE DIVERGENCIAS%' OR UPPER(REP_N2_RESP.description) LIKE '%ACOMPANHAMENTO SAT - APONTAMENTO DE DIVERGENCIAS%' OR
                                                                           UPPER(REP_N2_RESP.description) LIKE '%ACOMPANHAMENTO SAT COM SUPORTE%')
               LEFT JOIN users AS REP_N2_RESP_USER
                         ON REP_N2_RESP.created_by=REP_N2_RESP_USER.id
               
               LEFT JOIN (SELECT reports.assignment_id, COUNT(reports.assignment_id) AS QTDA_AGENDA 
               FROM reports 
                         WHERE reports.description LIKE '%Solicitação agendada para%' OR reports.description LIKE '%Solicitação reagendada para%'
                         GROUP BY reports.assignment_id
                    ) AS REP_AGENDA
               ON REP_AGENDA.assignment_id=SOLICITACOES.id
               
               WHERE 
                    contracts.company_place_id=11
                    AND contracts.contract_number = :numero_contrato) AS GERAL
               WHERE
                    STATUS_ATUAL = 'ANDAMENTO';
          ";
                
		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':numero_contrato', $numero_contrato);
          $stmt->execute();

          return $stmt->fetchAll(\PDO::FETCH_ASSOC);
     }
}
?>