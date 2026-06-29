import React, { useCallback, useEffect, useState } from 'react';
import {
  View, Text, FlatList, StyleSheet, TouchableOpacity, ActivityIndicator, RefreshControl, Alert,
} from 'react-native';
import { useNavigation } from '@react-navigation/native';
import type { NativeStackNavigationProp } from '@react-navigation/native-stack';
import { solicitacaoAdocaoService } from '../../api/services/solicitacaoAdocaoService';
import { EmptyState } from '../../components/ui/EmptyState';
import { colors } from '../../theme/colors';
import { typography } from '../../theme/typography';
import { spacing } from '../../theme/spacing';
import type { SolicitacaoAdocao, StatusSolicitacao } from '../../types/SolicitacaoAdocao';
import type { AdocoesOngStackParamList } from '../../navigation/stacks/AdocoesOngStack';

type Nav = NativeStackNavigationProp<AdocoesOngStackParamList, 'AdocoesOng'>;

const STATUS_COR: Record<string, string> = {
  'Pendente': '#FFC107',
  'Em Análise': colors.info,
  'Aprovado': colors.success,
  'Concluído': colors.primary,
  'Recusado': colors.error,
};

const PROXIMOS_STATUS: Partial<Record<StatusSolicitacao, StatusSolicitacao>> = {
  'Pendente': 'Em Análise',
  'Em Análise': 'Aprovado',
};

function SolicitacaoOngCard({
  item,
  onAvancar,
  onRecusar,
  onAvaliar,
}: {
  item: SolicitacaoAdocao;
  onAvancar: () => void;
  onRecusar: () => void;
  onAvaliar: () => void;
}) {
  const proximoStatus = PROXIMOS_STATUS[item.status];

  return (
    <View style={styles.card}>
      <View style={styles.cardHeader}>
        <View>
          <Text style={styles.adotanteNome}>{item.adotante_nome ?? `Adotante #${item.fk_adotante_id}`}</Text>
          <Text style={styles.adotanteEmail}>{item.adotante_email ?? ''}</Text>
        </View>
        <View style={[styles.statusBadge, { backgroundColor: STATUS_COR[item.status] ?? colors.border }]}>
          <Text style={styles.statusLabel}>{item.status}</Text>
        </View>
      </View>

      <Text style={styles.animalNome}>Animal: {item.animal?.nome ?? `#${item.fk_animal_id}`}</Text>
      <Text style={styles.motivo} numberOfLines={2}>{item.motivo}</Text>
      <Text style={styles.data}>{new Date(item.data).toLocaleDateString('pt-BR')}</Text>

      <View style={styles.acoes}>
        {proximoStatus && (
          <TouchableOpacity style={styles.btnAvancar} onPress={onAvancar} activeOpacity={0.8}>
            <Text style={styles.btnAvancarLabel}>→ {proximoStatus}</Text>
          </TouchableOpacity>
        )}
        {item.status === 'Em Análise' && (
          <TouchableOpacity style={styles.btnAvaliar} onPress={onAvaliar} activeOpacity={0.8}>
            <Text style={styles.btnAvaliarLabel}>Avaliar</Text>
          </TouchableOpacity>
        )}
        {(item.status === 'Pendente' || item.status === 'Em Análise') && (
          <TouchableOpacity style={styles.btnRecusar} onPress={onRecusar} activeOpacity={0.8}>
            <Text style={styles.btnRecusarLabel}>Recusar</Text>
          </TouchableOpacity>
        )}
      </View>
    </View>
  );
}

export function AdocoesOngScreen() {
  const navigation = useNavigation<Nav>();
  const [solicitacoes, setSolicitacoes] = useState<SolicitacaoAdocao[]>([]);
  const [carregando, setCarregando] = useState(true);
  const [atualizando, setAtualizando] = useState(false);
  const [erro, setErro] = useState<string | null>(null);

  const carregar = useCallback(async (refresh = false) => {
    if (refresh) setAtualizando(true);
    else setCarregando(true);
    setErro(null);
    try {
      setSolicitacoes(await solicitacaoAdocaoService.recebidas());
    } catch (err: any) {
      setErro(err?.message ?? 'Erro ao carregar solicitações.');
    } finally { setCarregando(false); setAtualizando(false); }
  }, []);

  useEffect(() => { carregar(); }, [carregar]);

  async function avancar(id: number, status: StatusSolicitacao) {
    try {
      const atualizada = await solicitacaoAdocaoService.avancarStatus(id, { status });
      setSolicitacoes(prev => prev.map(s => s.id === id ? atualizada : s));
    } catch { Alert.alert('Erro', 'Não foi possível atualizar o status.'); }
  }

  async function recusar(id: number) {
    Alert.alert('Recusar solicitação', 'Confirma que deseja recusar esta solicitação?', [
      { text: 'Cancelar', style: 'cancel' },
      { text: 'Recusar', style: 'destructive', onPress: async () => { await avancar(id, 'Recusado'); } },
    ]);
  }

  if (carregando) return <View style={styles.centered}><ActivityIndicator size="large" color={colors.primary} /></View>;

  if (erro) return (
    <View style={styles.centered}>
      <Text style={styles.erroTexto}>{erro}</Text>
      <TouchableOpacity style={styles.btnRetry} onPress={() => carregar()}><Text style={styles.btnRetryLabel}>Tentar novamente</Text></TouchableOpacity>
    </View>
  );

  return (
    <View style={styles.root}>
      <FlatList
        data={solicitacoes}
        keyExtractor={item => String(item.id)}
        renderItem={({ item }) => (
          <SolicitacaoOngCard
            item={item}
            onAvancar={() => {
              const prox = PROXIMOS_STATUS[item.status];
              if (prox) avancar(item.id, prox);
            }}
            onRecusar={() => recusar(item.id)}
            onAvaliar={() => navigation.navigate('AvaliacaoAdotante', {
              solicitacaoId: item.id,
              adotanteNome: item.adotante_nome ?? `Adotante #${item.fk_adotante_id}`,
            })}
          />
        )}
        contentContainerStyle={styles.lista}
        refreshControl={<RefreshControl refreshing={atualizando} onRefresh={() => carregar(true)} colors={[colors.primary]} />}
        ListEmptyComponent={
          <EmptyState
            icon="📋"
            title="Nenhuma solicitação recebida"
            message="Quando adotantes solicitarem adoção, aparecerá aqui."
          />
        }
      />
    </View>
  );
}

const styles = StyleSheet.create({
  root: { flex: 1, backgroundColor: colors.bgMuted },
  lista: { padding: spacing.md, paddingBottom: spacing.xxl },
  card: {
    backgroundColor: colors.bg, borderRadius: 12, padding: spacing.md, marginBottom: spacing.sm,
    elevation: 1, shadowColor: colors.black, shadowOffset: { width: 0, height: 1 }, shadowOpacity: 0.06, shadowRadius: 3,
  },
  cardHeader: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'flex-start', marginBottom: spacing.xs },
  adotanteNome: { fontFamily: typography.fontFamily.titleBold, fontSize: typography.fontSize.md, color: colors.text },
  adotanteEmail: { fontFamily: typography.fontFamily.body, fontSize: typography.fontSize.xs, color: colors.secondary },
  statusBadge: { borderRadius: 10, paddingVertical: 3, paddingHorizontal: 8 },
  statusLabel: { fontFamily: typography.fontFamily.bodyBold, fontSize: typography.fontSize.xs, color: colors.white },
  animalNome: { fontFamily: typography.fontFamily.bodyBold, fontSize: typography.fontSize.sm, color: colors.text, marginBottom: 2 },
  motivo: { fontFamily: typography.fontFamily.body, fontSize: typography.fontSize.sm, color: colors.secondary, marginBottom: 4 },
  data: { fontFamily: typography.fontFamily.body, fontSize: typography.fontSize.xs, color: colors.secondary },
  acoes: { flexDirection: 'row', gap: spacing.sm, marginTop: spacing.sm, flexWrap: 'wrap' },
  btnAvancar: { backgroundColor: colors.primary, borderRadius: 8, paddingVertical: 6, paddingHorizontal: spacing.md },
  btnAvancarLabel: { fontFamily: typography.fontFamily.bodyBold, fontSize: typography.fontSize.sm, color: colors.white },
  btnAvaliar: { backgroundColor: colors.info, borderRadius: 8, paddingVertical: 6, paddingHorizontal: spacing.md },
  btnAvaliarLabel: { fontFamily: typography.fontFamily.bodyBold, fontSize: typography.fontSize.sm, color: colors.white },
  btnRecusar: { borderWidth: 1, borderColor: colors.error, borderRadius: 8, paddingVertical: 6, paddingHorizontal: spacing.md },
  btnRecusarLabel: { fontFamily: typography.fontFamily.bodyBold, fontSize: typography.fontSize.sm, color: colors.error },
  centered: { flex: 1, alignItems: 'center', justifyContent: 'center', padding: spacing.lg },
  erroTexto: { fontFamily: typography.fontFamily.body, fontSize: typography.fontSize.md, color: colors.error, textAlign: 'center', marginBottom: spacing.md },
  btnRetry: { backgroundColor: colors.primary, borderRadius: 8, paddingVertical: spacing.sm, paddingHorizontal: spacing.lg },
  btnRetryLabel: { fontFamily: typography.fontFamily.bodyBold, fontSize: typography.fontSize.md, color: colors.white },
});
