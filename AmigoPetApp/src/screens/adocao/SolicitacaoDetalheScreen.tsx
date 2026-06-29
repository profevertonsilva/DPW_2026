import React, { useCallback, useEffect, useState } from 'react';
import {
  View, Text, ScrollView, StyleSheet, TouchableOpacity, ActivityIndicator, Linking, Alert,
} from 'react-native';
import { useNavigation, useRoute } from '@react-navigation/native';
import type { NativeStackNavigationProp } from '@react-navigation/native-stack';
import type { RouteProp } from '@react-navigation/native';
import { solicitacaoAdocaoService } from '../../api/services/solicitacaoAdocaoService';
import { colors } from '../../theme/colors';
import { typography } from '../../theme/typography';
import { spacing } from '../../theme/spacing';
import type { SolicitacaoAdocao } from '../../types/SolicitacaoAdocao';
import type { AdocaoStackParamList } from '../../navigation/stacks/AdocaoStack';

type Nav = NativeStackNavigationProp<AdocaoStackParamList, 'SolicitacaoDetalhe'>;
type Route = RouteProp<AdocaoStackParamList, 'SolicitacaoDetalhe'>;

const STATUS_COR: Record<string, string> = {
  'Pendente': '#FFC107',
  'Em Análise': colors.info,
  'Aprovado': colors.success,
  'Concluído': colors.primary,
  'Recusado': colors.error,
};

const TIMELINE = ['Pendente', 'Em Análise', 'Aprovado', 'Concluído'];

function formatData(iso: string) {
  return new Date(iso).toLocaleString('pt-BR', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' });
}

export function SolicitacaoDetalheScreen() {
  const navigation = useNavigation<Nav>();
  const route = useRoute<Route>();
  const { id } = route.params;

  const [sol, setSol] = useState<SolicitacaoAdocao | null>(null);
  const [carregando, setCarregando] = useState(true);
  const [erro, setErro] = useState<string | null>(null);

  const carregar = useCallback(async () => {
    setCarregando(true);
    setErro(null);
    try {
      setSol(await solicitacaoAdocaoService.buscarPorId(id));
    } catch (err: any) {
      setErro(err?.message ?? 'Erro ao carregar solicitação.');
    } finally { setCarregando(false); }
  }, [id]);

  useEffect(() => { carregar(); }, [carregar]);

  if (carregando) return <View style={styles.centered}><ActivityIndicator size="large" color={colors.primary} /></View>;
  if (erro || !sol) return (
    <View style={styles.centered}>
      <Text style={styles.erroTexto}>{erro ?? 'Solicitação não encontrada.'}</Text>
      <TouchableOpacity style={styles.btnRetry} onPress={carregar}><Text style={styles.btnRetryLabel}>Tentar novamente</Text></TouchableOpacity>
    </View>
  );

  const statusIdx = TIMELINE.indexOf(sol.status);
  const isAprovado = sol.status === 'Aprovado';
  const isConcluido = sol.status === 'Concluído';
  const isRecusado = sol.status === 'Recusado';

  return (
    <ScrollView style={styles.root} contentContainerStyle={styles.scroll}>
      {/* Status badge */}
      <View style={[styles.statusCard, { borderLeftColor: STATUS_COR[sol.status] ?? colors.border }]}>
        <View style={[styles.statusDot, { backgroundColor: STATUS_COR[sol.status] ?? colors.border }]} />
        <Text style={styles.statusLabel}>{sol.status}</Text>
      </View>

      {/* Timeline */}
      {!isRecusado && (
        <View style={styles.timeline}>
          {TIMELINE.map((s, i) => (
            <View key={s} style={styles.timelineItem}>
              <View style={[styles.timelineDot, i <= statusIdx && { backgroundColor: colors.primary }]} />
              {i < TIMELINE.length - 1 && (
                <View style={[styles.timelineLine, i < statusIdx && { backgroundColor: colors.primary }]} />
              )}
              <Text style={[styles.timelineLabel, i <= statusIdx && { color: colors.primary, fontFamily: typography.fontFamily.bodyBold }]}>{s}</Text>
            </View>
          ))}
        </View>
      )}
      {isRecusado && (
        <View style={styles.recusadoAviso}>
          <Text style={styles.recusadoAvisoTexto}>Esta solicitação foi recusada.</Text>
        </View>
      )}

      {/* Animal */}
      <View style={styles.secao}>
        <Text style={styles.secaoTitulo}>Animal</Text>
        <Text style={styles.secaoBody}>{sol.animal?.nome ?? `ID #${sol.fk_animal_id}`}</Text>
        {sol.animal?.especie && <Text style={styles.secaoSub}>{sol.animal.especie}{sol.animal.raca ? ` · ${sol.animal.raca}` : ''}</Text>}
      </View>

      {/* Motivo */}
      <View style={styles.secao}>
        <Text style={styles.secaoTitulo}>Motivo informado</Text>
        <Text style={styles.secaoBody}>{sol.motivo}</Text>
      </View>

      <Text style={styles.dataText}>Solicitado em {formatData(sol.data)}</Text>

      {/* Ação: assinar termo */}
      {isAprovado && !sol.termo_assinado && (
        <TouchableOpacity style={styles.btnTermo}
          onPress={() => navigation.navigate('TermoDigital', { solicitacaoId: id })}
          activeOpacity={0.85}>
          <Text style={styles.btnTermoLabel}>✍️ Assinar Termo de Responsabilidade</Text>
          <Text style={styles.btnTermoSub}>Obrigatório para concluir a adoção</Text>
        </TouchableOpacity>
      )}

      {/* Ver termo assinado */}
      {(isConcluido || sol.termo_assinado) && sol.pdf_termo_url && (
        <TouchableOpacity style={styles.btnVerTermo}
          onPress={() => Linking.openURL(sol.pdf_termo_url!)}
          activeOpacity={0.8}>
          <Text style={styles.btnVerTermoLabel}>📄 Ver Termo Assinado (PDF)</Text>
        </TouchableOpacity>
      )}

      {isConcluido && (
        <View style={styles.parabensCard}>
          <Text style={styles.parabensEmoji}>🎉</Text>
          <Text style={styles.parabensTexto}>Adoção concluída! Bem-vindo ao novo integrante da família.</Text>
        </View>
      )}
    </ScrollView>
  );
}

const styles = StyleSheet.create({
  root: { flex: 1, backgroundColor: colors.bgMuted },
  scroll: { padding: spacing.md, paddingBottom: spacing.xxl },
  statusCard: {
    flexDirection: 'row', alignItems: 'center', backgroundColor: colors.bg, borderRadius: 12,
    padding: spacing.md, marginBottom: spacing.md, borderLeftWidth: 5,
    elevation: 1,
  },
  statusDot: { width: 12, height: 12, borderRadius: 6, marginRight: spacing.sm },
  statusLabel: { fontFamily: typography.fontFamily.titleBold, fontSize: typography.fontSize.lg, color: colors.text },
  timeline: { flexDirection: 'row', alignItems: 'flex-start', backgroundColor: colors.bg, borderRadius: 12, padding: spacing.md, marginBottom: spacing.md },
  timelineItem: { flex: 1, alignItems: 'center' },
  timelineDot: { width: 12, height: 12, borderRadius: 6, backgroundColor: colors.border, marginBottom: 4 },
  timelineLine: { position: 'absolute', top: 5, left: '50%', width: '100%', height: 2, backgroundColor: colors.border },
  timelineLabel: { fontFamily: typography.fontFamily.body, fontSize: typography.fontSize.xs, color: colors.secondary, textAlign: 'center' },
  recusadoAviso: { backgroundColor: '#FFF0F0', borderRadius: 10, padding: spacing.md, marginBottom: spacing.md, borderLeftWidth: 4, borderLeftColor: colors.error },
  recusadoAvisoTexto: { fontFamily: typography.fontFamily.body, fontSize: typography.fontSize.sm, color: colors.error },
  secao: {
    backgroundColor: colors.bg, borderRadius: 12, padding: spacing.md, marginBottom: spacing.sm,
    elevation: 1, shadowColor: colors.black, shadowOffset: { width: 0, height: 1 }, shadowOpacity: 0.06, shadowRadius: 3,
  },
  secaoTitulo: { fontFamily: typography.fontFamily.bodyBold, fontSize: typography.fontSize.sm, color: colors.secondary, textTransform: 'uppercase', letterSpacing: 0.5, marginBottom: 4 },
  secaoBody: { fontFamily: typography.fontFamily.body, fontSize: typography.fontSize.md, color: colors.text, lineHeight: 22 },
  secaoSub: { fontFamily: typography.fontFamily.body, fontSize: typography.fontSize.sm, color: colors.secondary, marginTop: 2 },
  dataText: { fontFamily: typography.fontFamily.body, fontSize: typography.fontSize.xs, color: colors.secondary, textAlign: 'center', marginVertical: spacing.sm },
  btnTermo: {
    backgroundColor: colors.success, borderRadius: 12, padding: spacing.md,
    alignItems: 'center', marginTop: spacing.sm,
    elevation: 2, shadowColor: colors.black, shadowOffset: { width: 0, height: 2 }, shadowOpacity: 0.1, shadowRadius: 4,
  },
  btnTermoLabel: { fontFamily: typography.fontFamily.titleBold, fontSize: typography.fontSize.md, color: colors.white },
  btnTermoSub: { fontFamily: typography.fontFamily.body, fontSize: typography.fontSize.xs, color: 'rgba(255,255,255,0.85)', marginTop: 2 },
  btnVerTermo: { alignItems: 'center', paddingVertical: spacing.sm, marginTop: spacing.sm },
  btnVerTermoLabel: { fontFamily: typography.fontFamily.bodyBold, fontSize: typography.fontSize.sm, color: colors.primary },
  parabensCard: {
    backgroundColor: '#F0FFF4', borderRadius: 12, padding: spacing.lg,
    alignItems: 'center', marginTop: spacing.md,
    borderWidth: 1, borderColor: colors.success,
  },
  parabensEmoji: { fontSize: 40, marginBottom: spacing.sm },
  parabensTexto: { fontFamily: typography.fontFamily.bodyBold, fontSize: typography.fontSize.md, color: colors.success, textAlign: 'center' },
  centered: { flex: 1, alignItems: 'center', justifyContent: 'center', padding: spacing.lg },
  erroTexto: { fontFamily: typography.fontFamily.body, fontSize: typography.fontSize.md, color: colors.error, textAlign: 'center', marginBottom: spacing.md },
  btnRetry: { backgroundColor: colors.primary, borderRadius: 8, paddingVertical: spacing.sm, paddingHorizontal: spacing.lg },
  btnRetryLabel: { fontFamily: typography.fontFamily.bodyBold, fontSize: typography.fontSize.md, color: colors.white },
});
