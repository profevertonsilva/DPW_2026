import React, { useEffect, useState } from 'react';
import {
  View, Text, ScrollView, StyleSheet, ActivityIndicator, TouchableOpacity, Share,
} from 'react-native';
import { NativeStackScreenProps } from '@react-navigation/native-stack';
import { HomeStackParamList } from '../../navigation/stacks/HomeStack';
import { Avatar } from '../../components/ui/Avatar';
import { Card } from '../../components/ui/Card';
import { Section } from '../../components/ui/Section';
import { PermissionGate } from '../../components/ui/PermissionGate';
import { usePermissions } from '../../permissions/usePermissions';
import { animalService } from '../../api/services/animalService';
import { vacinaService } from '../../api/services/vacinaService';
import { procedimentoService } from '../../api/services/procedimentoService';
import { saudeService } from '../../api/services/saudeService';
import { auditoriaService } from '../../api/services/auditoriaService';
import type { Animal } from '../../types/Animal';
import type { Vacina, Procedimento, SaudeAnimal, AuditoriaEntry } from '../../types/Saude';
import { colors } from '../../theme/colors';
import { typography } from '../../theme/typography';
import { spacing } from '../../theme/spacing';

type Props = NativeStackScreenProps<HomeStackParamList, 'AnimalDetalhe'>;

const PORTE_LABEL: Record<string, string> = { pequeno: 'Pequeno', medio: 'Médio', grande: 'Grande', gigante: 'Gigante' };
const SEXO_LABEL: Record<string, string> = { m: 'Macho', f: 'Fêmea' };
const STATUS_LABEL: Record<string, string> = {
  disponivel: 'Disponível', adotado: 'Adotado', em_tratamento: 'Em Tratamento', reservado: 'Reservado',
};
// Cor semântica por status (tokens do theme)
const STATUS_COR: Record<string, string> = {
  disponivel: colors.primary, adotado: colors.info, em_tratamento: colors.accent, reservado: colors.secondary,
};
const TIPO_PROC_LABEL: Record<string, string> = {
  consulta: 'Consulta', cirurgia: 'Cirurgia', exame: 'Exame', castracao: 'Castração', outro: 'Outro',
};
const AUDITORIA_LABEL: Record<string, string> = {
  registro_inicial: 'Registro inicial',
  atualizacao_pos_tratamento: 'Atualização pós-tratamento',
  vacinacao: 'Vacinação',
  procedimento: 'Procedimento',
  adocao: 'Adoção',
  resgate: 'Resgate',
  outros: 'Outros',
};

function diasParaReforco(dataReforco: string): number {
  return Math.floor((new Date(dataReforco).getTime() - Date.now()) / 86400000);
}

function alertaReforco(dataReforco: string | null): 'critico' | 'alerta' | 'proximo' | null {
  if (!dataReforco) return null;
  const dias = diasParaReforco(dataReforco);
  if (dias <= 7) return 'critico';
  if (dias <= 15) return 'alerta';
  if (dias <= 30) return 'proximo';
  return null;
}

const ALERTA_COR = { critico: colors.error, alerta: colors.accent, proximo: '#FFC107' };
const ALERTA_TEXTO = {
  critico: 'Reforço vencido ou em 7 dias!',
  alerta: 'Reforço em 8–15 dias',
  proximo: 'Reforço em 16–30 dias',
};

export function AnimalDetalheScreen({ route, navigation }: Props) {
  const { id } = route.params;
  const { has } = usePermissions();
  const [animal, setAnimal] = useState<Animal | null>(null);
  const [vacinas, setVacinas] = useState<Vacina[]>([]);
  const [procedimentos, setProcedimentos] = useState<Procedimento[]>([]);
  const [saude, setSaude] = useState<SaudeAnimal | null>(null);
  const [auditoria, setAuditoria] = useState<AuditoriaEntry[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    (async () => {
      try {
        const [a, v, p, s, au] = await Promise.all([
          animalService.buscarPorId(id),
          vacinaService.listar(id),
          procedimentoService.listar(id),
          saudeService.obter(id),
          auditoriaService.listar(id),
        ]);
        setAnimal(a);
        setVacinas(v);
        setProcedimentos(p);
        setSaude(s);
        setAuditoria(au);
      } finally {
        setLoading(false);
      }
    })();
  }, [id]);

  if (loading) {
    return <View style={styles.center}><ActivityIndicator size="large" color={colors.primary} /></View>;
  }

  if (!animal) {
    return <View style={styles.center}><Text style={styles.erroText}>Animal não encontrado.</Text></View>;
  }

  const disponivel = animal.status === 'disponivel';

  return (
    <View style={styles.root}>
      <ScrollView contentContainerStyle={styles.scroll}>
        {/* Hero */}
        <View style={styles.hero}>
          <Avatar uri={animal.foto} nome={animal.nome} size={100} />
          <Text style={styles.nome}>{animal.nome}</Text>
          {animal.raca ? <Text style={styles.raca}>{animal.raca}</Text>
            : animal.especie ? <Text style={styles.raca}>{animal.especie}</Text> : null}
          <View style={[styles.statusBadge, { backgroundColor: STATUS_COR[animal.status] ?? colors.secondary }]}>
            <Text style={[styles.statusLabel, { color: colors.white }]}>
              {STATUS_LABEL[animal.status] ?? animal.status}
            </Text>
          </View>
        </View>

        {/* Informações básicas */}
        <Section titulo="Informações">
          <Card>
            <View style={styles.infoGrid}>
              <InfoItem label="Espécie" value={animal.especie ?? '—'} />
              <InfoItem label="Porte" value={animal.porte ? PORTE_LABEL[animal.porte] ?? animal.porte : '—'} />
              <InfoItem label="Sexo" value={animal.sexo ? SEXO_LABEL[animal.sexo] ?? animal.sexo : '—'} />
              <InfoItem label="Localização" value={animal.localizacao ?? '—'} />
              {animal.ong && <InfoItem label="ONG" value={animal.ong.nome} />}
            </View>
          </Card>
        </Section>

        {/* Saúde Geral (RF#09) */}
        <Section titulo="Saúde Geral">
          <Card>
            {saude ? (
              <View>
                <View style={styles.aptidaoRow}>
                  <Text style={styles.aptidaoLabel}>Apto para adoção:</Text>
                  <View style={[styles.aptidaoBadge, { backgroundColor: saude.apto_para_adocao ? colors.success : colors.error }]}>
                    <Text style={styles.aptidaoBadgeLabel}>{saude.apto_para_adocao ? 'Sim' : 'Não'}</Text>
                  </View>
                </View>
                {saude.temperamento && <InfoItem label="Temperamento" value={saude.temperamento} />}
                {saude.necessidades_especiais && <InfoItem label="Necessidades especiais" value={saude.necessidades_especiais} />}
              </View>
            ) : (
              <SemDados texto="Sem informações de saúde registradas." />
            )}
            {(has('podeCadastrarAnimal') || has('podeRegistrarProcedimento')) && (
              <TouchableOpacity style={styles.btnEditar}
                onPress={() => navigation.navigate('EditarSaudeAnimal', { animalId: id })}>
                <Text style={styles.btnEditarLabel}>✏️ Editar condição geral</Text>
              </TouchableOpacity>
            )}
          </Card>
        </Section>

        {/* Vacinas (RF#15) */}
        <Section titulo="Vacinas">
          <Card>
          {vacinas.length === 0 ? (
            <SemDados texto="Nenhuma vacina registrada." />
          ) : (
            vacinas.map(v => {
              const nivel = alertaReforco(v.data_reforco);
              return (
                <View key={v.id} style={[styles.vacinaCard, nivel && { borderLeftColor: ALERTA_COR[nivel], borderLeftWidth: 4 }]}>
                  <View style={styles.vacinaHeader}>
                    <Text style={styles.vacinaNome}>💉 {v.nome}</Text>
                    {nivel && (
                      <View style={[styles.alertaBadge, { backgroundColor: ALERTA_COR[nivel] }]}>
                        <Text style={styles.alertaBadgeLabel}>{ALERTA_TEXTO[nivel]}</Text>
                      </View>
                    )}
                  </View>
                  <Text style={styles.vacinaSub}>Aplicada: {new Date(v.data_aplicacao).toLocaleDateString('pt-BR')}</Text>
                  {v.data_reforco && (
                    <Text style={[styles.vacinaSub, nivel && { color: ALERTA_COR[nivel] }]}>
                      Reforço: {new Date(v.data_reforco).toLocaleDateString('pt-BR')}
                    </Text>
                  )}
                  {v.veterinario_nome && <Text style={styles.vacinaSub}>Vet: {v.veterinario_nome}</Text>}
                  {v.clinica_nome && <Text style={styles.vacinaSub}>Clínica: {v.clinica_nome}</Text>}
                  <Text style={styles.vacinaAutor}>por {v.criado_por}</Text>
                </View>
              );
            })
          )}
          <PermissionGate capability="podeRegistrarVacina">
            <TouchableOpacity style={styles.btnAdicionar}
              onPress={() => navigation.navigate('AdicionarVacina', { animalId: id })}>
              <Text style={styles.btnAdicionarLabel}>+ Adicionar vacina</Text>
            </TouchableOpacity>
          </PermissionGate>
          <PermissionGate capability="podeCadastrarAnimal">
            <TouchableOpacity style={styles.btnAdicionar}
              onPress={() => navigation.navigate('AdicionarVacina', { animalId: id })}>
              <Text style={styles.btnAdicionarLabel}>+ Adicionar vacina</Text>
            </TouchableOpacity>
          </PermissionGate>
          </Card>
        </Section>

        {/* Procedimentos (RF#16) */}
        <Section titulo="Procedimentos Médicos">
          <Card>
          {procedimentos.length === 0 ? (
            <SemDados texto="Nenhum procedimento registrado." />
          ) : (
            procedimentos.map(p => (
              <View key={p.id} style={styles.procCard}>
                <View style={styles.procHeader}>
                  <Text style={styles.procNome}>🩺 {p.nome}</Text>
                  <View style={styles.tipoBadge}>
                    <Text style={styles.tipoBadgeLabel}>{TIPO_PROC_LABEL[p.tipo] ?? p.tipo}</Text>
                  </View>
                </View>
                <Text style={styles.procSub}>{new Date(p.data).toLocaleDateString('pt-BR')}</Text>
                {p.veterinario_nome && <Text style={styles.procSub}>Vet: {p.veterinario_nome}</Text>}
                {p.observacoes && <Text style={styles.procObs}>{p.observacoes}</Text>}
                {p.anexo_url && <Text style={styles.procAnexo}>📎 Anexo disponível</Text>}
                <Text style={styles.procAutor}>por {p.criado_por}</Text>
              </View>
            ))
          )}
          <PermissionGate capability="podeRegistrarProcedimento">
            <TouchableOpacity style={styles.btnAdicionar}
              onPress={() => navigation.navigate('AdicionarProcedimento', { animalId: id })}>
              <Text style={styles.btnAdicionarLabel}>+ Adicionar procedimento</Text>
            </TouchableOpacity>
          </PermissionGate>
          <PermissionGate capability="podeCadastrarAnimal">
            <TouchableOpacity style={styles.btnAdicionar}
              onPress={() => navigation.navigate('AdicionarProcedimento', { animalId: id })}>
              <Text style={styles.btnAdicionarLabel}>+ Adicionar procedimento</Text>
            </TouchableOpacity>
          </PermissionGate>
          </Card>
        </Section>

        {/* Compartilhar perfil (RF#21) */}
        <TouchableOpacity style={styles.btnCompartilhar}
          onPress={() => Share.share({
            title: `${animal.nome} — AmigoPet`,
            message: `Conheça ${animal.nome} e ajude a encontrar um lar! https://amigopet.com/animal/${animal.id}`,
            url: `https://amigopet.com/animal/${animal.id}`,
          })}
          activeOpacity={0.85}>
          <Text style={styles.btnCompartilharLabel}>↗ Compartilhar este animal</Text>
        </TouchableOpacity>

        {/* Carteira de Identificação (RF#17) */}
        <TouchableOpacity style={styles.btnCarteira}
          onPress={() => navigation.navigate('CarteiraIdentificacao', { animalId: id })}
          activeOpacity={0.85}>
          <Text style={styles.btnCarteiraLabel}>🪪 Ver Carteira de Identificação</Text>
        </TouchableOpacity>

        {/* Transferência de responsabilidade (RF#24) */}
        <PermissionGate capability="podeCadastrarAnimal">
          <TouchableOpacity style={styles.btnTransferencia}
            onPress={() => navigation.navigate('Transferencia', { animalId: id })}
            activeOpacity={0.85}>
            <Text style={styles.btnTransferenciaLabel}>↗ Transferir responsabilidade</Text>
          </TouchableOpacity>
        </PermissionGate>

        {/* Log de Auditoria (RF#10) */}
        <Section titulo="Histórico (imutável)">
          <Card>
          <View style={styles.imutavelAviso}>
            <Text style={styles.imutavelAvisoTexto}>🔒 Registro de auditoria — somente leitura</Text>
          </View>
          {auditoria.length === 0 ? (
            <SemDados texto="Sem registros de auditoria." />
          ) : (
            auditoria.map(e => (
              <View key={e.id} style={styles.auditoriaItem}>
                <View style={styles.auditoriaLinha} />
                <View style={styles.auditoriaConteudo}>
                  <Text style={styles.auditoriaTipo}>{AUDITORIA_LABEL[e.tipo] ?? e.tipo}</Text>
                  <Text style={styles.auditoriaDesc}>{e.descricao}</Text>
                  <Text style={styles.auditoriaMeta}>
                    {e.autor_nome} · {new Date(e.data).toLocaleString('pt-BR', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' })}
                  </Text>
                </View>
              </View>
            ))
          )}
          </Card>
        </Section>
      </ScrollView>

      {/* Footer */}
      <View style={styles.footer}>
        <TouchableOpacity
          style={[styles.btnSolicitar, !disponivel && styles.btnDisabled]}
          disabled={!disponivel}
          onPress={() => navigation.navigate('SolicitarAdocao', { animalId: id })}
          activeOpacity={0.85}
        >
          <Text style={styles.btnLabel}>
            {disponivel ? 'Solicitar Adoção' : 'Indisponível para adoção'}
          </Text>
        </TouchableOpacity>
      </View>
    </View>
  );
}

function InfoItem({ label, value }: { label: string; value: string }) {
  return (
    <View style={styles.infoItem}>
      <Text style={styles.infoLabel}>{label}</Text>
      <Text style={styles.infoValue} numberOfLines={2}>{value}</Text>
    </View>
  );
}

function SemDados({ texto }: { texto: string }) {
  return (
    <View style={styles.semDadosBox}>
      <Text style={styles.semDadosIcon}>🐾</Text>
      <Text style={styles.semDados}>{texto}</Text>
    </View>
  );
}

const styles = StyleSheet.create({
  root: { flex: 1, backgroundColor: colors.bgMuted },
  center: { flex: 1, justifyContent: 'center', alignItems: 'center' },
  erroText: { fontFamily: typography.fontFamily.body, color: colors.secondary, fontSize: typography.fontSize.md },
  scroll: { padding: spacing.md, paddingBottom: spacing.xxl },
  hero: { alignItems: 'center', paddingVertical: spacing.lg, backgroundColor: colors.bg, borderRadius: 12, marginBottom: spacing.md },
  nome: { fontFamily: typography.fontFamily.titleBold, fontSize: typography.fontSize.xl, color: colors.text, marginTop: spacing.sm },
  raca: { fontFamily: typography.fontFamily.body, fontSize: typography.fontSize.md, color: colors.secondary, marginTop: 2 },
  statusBadge: { marginTop: spacing.sm, paddingHorizontal: spacing.md, paddingVertical: 4, borderRadius: 12 },
  statusLabel: { fontFamily: typography.fontFamily.bodyBold, fontSize: typography.fontSize.sm },
  infoGrid: { gap: spacing.sm },
  infoItem: { flexDirection: 'row', justifyContent: 'space-between', paddingVertical: spacing.xs, borderBottomWidth: 1, borderBottomColor: colors.border },
  infoLabel: { fontFamily: typography.fontFamily.bodyBold, fontSize: typography.fontSize.sm, color: colors.secondary, flex: 1 },
  infoValue: { fontFamily: typography.fontFamily.body, fontSize: typography.fontSize.sm, color: colors.text, flex: 2, textAlign: 'right' },
  // Saúde Geral
  aptidaoRow: { flexDirection: 'row', alignItems: 'center', marginBottom: spacing.xs },
  aptidaoLabel: { fontFamily: typography.fontFamily.bodyBold, fontSize: typography.fontSize.sm, color: colors.secondary, marginRight: spacing.sm },
  aptidaoBadge: { borderRadius: 8, paddingVertical: 2, paddingHorizontal: spacing.sm },
  aptidaoBadgeLabel: { fontFamily: typography.fontFamily.bodyBold, fontSize: typography.fontSize.xs, color: colors.white },
  btnEditar: { marginTop: spacing.sm },
  btnEditarLabel: { fontFamily: typography.fontFamily.body, fontSize: typography.fontSize.sm, color: colors.primary },
  semDados: { fontFamily: typography.fontFamily.body, fontSize: typography.fontSize.sm, color: colors.secondary, fontStyle: 'italic', textAlign: 'center' },
  semDadosBox: { alignItems: 'center', paddingVertical: spacing.md },
  semDadosIcon: { fontSize: 28, marginBottom: spacing.xs, opacity: 0.5 },
  // Vacinas
  vacinaCard: {
    backgroundColor: colors.bgMuted, borderRadius: 10, padding: spacing.sm,
    marginBottom: spacing.sm, borderLeftWidth: 3, borderLeftColor: colors.border,
  },
  vacinaHeader: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', marginBottom: 4 },
  vacinaNome: { fontFamily: typography.fontFamily.bodyBold, fontSize: typography.fontSize.sm, color: colors.text, flex: 1 },
  alertaBadge: { borderRadius: 6, paddingVertical: 2, paddingHorizontal: 6, marginLeft: 4 },
  alertaBadgeLabel: { fontFamily: typography.fontFamily.bodyBold, fontSize: 9, color: colors.white },
  vacinaSub: { fontFamily: typography.fontFamily.body, fontSize: typography.fontSize.xs, color: colors.secondary },
  vacinaAutor: { fontFamily: typography.fontFamily.body, fontSize: 10, color: colors.border, marginTop: 4, fontStyle: 'italic' },
  // Procedimentos
  procCard: {
    backgroundColor: colors.bgMuted, borderRadius: 10, padding: spacing.sm, marginBottom: spacing.sm,
    borderLeftWidth: 3, borderLeftColor: colors.info,
  },
  procHeader: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', marginBottom: 4 },
  procNome: { fontFamily: typography.fontFamily.bodyBold, fontSize: typography.fontSize.sm, color: colors.text, flex: 1 },
  tipoBadge: { backgroundColor: colors.info, borderRadius: 6, paddingVertical: 2, paddingHorizontal: 6 },
  tipoBadgeLabel: { fontFamily: typography.fontFamily.bodyBold, fontSize: 9, color: colors.white },
  procSub: { fontFamily: typography.fontFamily.body, fontSize: typography.fontSize.xs, color: colors.secondary },
  procObs: { fontFamily: typography.fontFamily.body, fontSize: typography.fontSize.xs, color: colors.text, marginTop: 4 },
  procAnexo: { fontFamily: typography.fontFamily.body, fontSize: typography.fontSize.xs, color: colors.primary, marginTop: 2 },
  procAutor: { fontFamily: typography.fontFamily.body, fontSize: 10, color: colors.border, marginTop: 4, fontStyle: 'italic' },
  // Botões
  btnAdicionar: { marginTop: spacing.xs, paddingVertical: spacing.xs },
  btnAdicionarLabel: { fontFamily: typography.fontFamily.bodyBold, fontSize: typography.fontSize.sm, color: colors.primary },
  btnCompartilhar: {
    backgroundColor: colors.bg, borderRadius: 10, paddingVertical: spacing.md,
    alignItems: 'center', marginBottom: spacing.md,
    borderWidth: 1, borderColor: colors.info,
  },
  btnCompartilharLabel: { fontFamily: typography.fontFamily.body, fontSize: typography.fontSize.sm, color: colors.info },
  btnCarteira: {
    backgroundColor: colors.bg, borderRadius: 10, paddingVertical: spacing.md,
    alignItems: 'center', marginBottom: spacing.md,
    borderWidth: 1, borderColor: colors.primary,
    elevation: 1,
  },
  btnCarteiraLabel: { fontFamily: typography.fontFamily.bodyBold, fontSize: typography.fontSize.md, color: colors.primary },
  btnTransferencia: {
    backgroundColor: colors.bg, borderRadius: 10, paddingVertical: spacing.md,
    alignItems: 'center', marginBottom: spacing.md,
    borderWidth: 1, borderColor: colors.secondary,
  },
  btnTransferenciaLabel: { fontFamily: typography.fontFamily.body, fontSize: typography.fontSize.sm, color: colors.secondary },
  // Auditoria
  imutavelAviso: { backgroundColor: '#F5F5F5', borderRadius: 6, padding: spacing.sm, marginBottom: spacing.sm },
  imutavelAvisoTexto: { fontFamily: typography.fontFamily.body, fontSize: typography.fontSize.xs, color: colors.secondary },
  auditoriaItem: { flexDirection: 'row', marginBottom: spacing.sm },
  auditoriaLinha: { width: 2, backgroundColor: colors.border, marginRight: spacing.sm, borderRadius: 1 },
  auditoriaConteudo: { flex: 1 },
  auditoriaTipo: { fontFamily: typography.fontFamily.bodyBold, fontSize: typography.fontSize.sm, color: colors.text },
  auditoriaDesc: { fontFamily: typography.fontFamily.body, fontSize: typography.fontSize.sm, color: colors.text },
  auditoriaMeta: { fontFamily: typography.fontFamily.body, fontSize: 10, color: colors.secondary, marginTop: 2 },
  // Footer
  footer: { padding: spacing.md, backgroundColor: colors.bg, borderTopWidth: 1, borderTopColor: colors.border },
  btnSolicitar: { backgroundColor: colors.primary, borderRadius: 10, paddingVertical: spacing.md, alignItems: 'center' },
  btnDisabled: { backgroundColor: colors.border },
  btnLabel: { fontFamily: typography.fontFamily.bodyBold, fontSize: typography.fontSize.md, color: colors.white },
});
