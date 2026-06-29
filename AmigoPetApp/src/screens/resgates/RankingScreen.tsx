import React, { useCallback, useEffect, useState } from 'react';
import {
  View, Text, FlatList, StyleSheet, ActivityIndicator, TouchableOpacity, RefreshControl,
} from 'react-native';
import { rankingService } from '../../api/services/rankingService';
import { useAuth } from '../../hooks/useAuth';
import { EmptyState } from '../../components/ui/EmptyState';
import { colors } from '../../theme/colors';
import { typography } from '../../theme/typography';
import { spacing } from '../../theme/spacing';
import type { RankingItem } from '../../types/Avistamento';

const MEDAL: Record<number, string> = { 1: '🥇', 2: '🥈', 3: '🥉' };

function RankingCard({ item, destaque }: { item: RankingItem; destaque: boolean }) {
  return (
    <View style={[styles.card, destaque && styles.cardDestaque]}>
      <View style={styles.posicaoContainer}>
        <Text style={styles.posicao}>{MEDAL[item.posicao] ?? `#${item.posicao}`}</Text>
      </View>
      <View style={styles.cardInfo}>
        <Text style={[styles.nome, destaque && styles.nomeDestaque]}>
          {item.nome}{destaque ? '  (você)' : ''}
        </Text>
        <Text style={styles.sub}>{item.total_reports} avistamentos</Text>
      </View>
      <View style={styles.pontosContainer}>
        <Text style={[styles.pontos, destaque && styles.pontosDestaque]}>{item.pontos}</Text>
        <Text style={styles.pontosLabel}>pts</Text>
      </View>
    </View>
  );
}

export function RankingScreen() {
  const { user } = useAuth();
  const [ranking, setRanking] = useState<RankingItem[]>([]);
  const [carregando, setCarregando] = useState(true);
  const [atualizando, setAtualizando] = useState(false);
  const [erro, setErro] = useState<string | null>(null);

  const carregar = useCallback(async (refresh = false) => {
    if (refresh) setAtualizando(true);
    else setCarregando(true);
    setErro(null);
    try {
      const dados = await rankingService.listar();
      setRanking(dados);
    } catch (err: any) {
      setErro(err?.message ?? 'Erro ao carregar ranking.');
    } finally {
      setCarregando(false);
      setAtualizando(false);
    }
  }, []);

  useEffect(() => { carregar(); }, [carregar]);

  if (carregando) {
    return <View style={styles.centered}><ActivityIndicator size="large" color={colors.primary} /></View>;
  }

  if (erro) {
    return (
      <View style={styles.centered}>
        <Text style={styles.erroTexto}>{erro}</Text>
        <TouchableOpacity style={styles.btnRetry} onPress={() => carregar()}>
          <Text style={styles.btnRetryLabel}>Tentar novamente</Text>
        </TouchableOpacity>
      </View>
    );
  }

  return (
    <View style={styles.root}>
      <FlatList
        data={ranking}
        keyExtractor={item => String(item.usuario_id)}
        renderItem={({ item }) => (
          <RankingCard item={item} destaque={item.usuario_id === user?.id} />
        )}
        contentContainerStyle={styles.lista}
        refreshControl={<RefreshControl refreshing={atualizando} onRefresh={() => carregar(true)} colors={[colors.primary]} />}
        ListHeaderComponent={
          <View style={styles.header}>
            <Text style={styles.headerTitle}>Rastreadores mais ativos</Text>
            <Text style={styles.headerSub}>Usuários que mais reportaram animais de rua</Text>
          </View>
        }
        ListEmptyComponent={
          <EmptyState
            icon="🏆"
            title="Sem dados de ranking"
            message="Reporte animais de rua para aparecer aqui!"
          />
        }
      />
    </View>
  );
}

const styles = StyleSheet.create({
  root: { flex: 1, backgroundColor: colors.bgMuted },
  lista: { padding: spacing.md, paddingBottom: spacing.xxl },
  header: { marginBottom: spacing.md },
  headerTitle: { fontFamily: typography.fontFamily.titleBold, fontSize: typography.fontSize.xl, color: colors.text },
  headerSub: { fontFamily: typography.fontFamily.body, fontSize: typography.fontSize.sm, color: colors.secondary, marginTop: 2 },
  card: {
    flexDirection: 'row', alignItems: 'center', backgroundColor: colors.bg, borderRadius: 12,
    padding: spacing.md, marginBottom: spacing.sm,
    elevation: 1, shadowColor: colors.black, shadowOffset: { width: 0, height: 1 }, shadowOpacity: 0.06, shadowRadius: 3,
  },
  cardDestaque: {
    backgroundColor: '#FFF8F0', borderWidth: 2, borderColor: colors.accent,
  },
  posicaoContainer: { width: 40, alignItems: 'center' },
  posicao: { fontSize: 22 },
  cardInfo: { flex: 1, marginLeft: spacing.sm },
  nome: { fontFamily: typography.fontFamily.bodyBold, fontSize: typography.fontSize.md, color: colors.text },
  nomeDestaque: { color: colors.accent },
  sub: { fontFamily: typography.fontFamily.body, fontSize: typography.fontSize.sm, color: colors.secondary },
  pontosContainer: { alignItems: 'flex-end' },
  pontos: { fontFamily: typography.fontFamily.titleBold, fontSize: typography.fontSize.xl, color: colors.primary },
  pontosDestaque: { color: colors.accent },
  pontosLabel: { fontFamily: typography.fontFamily.body, fontSize: typography.fontSize.xs, color: colors.secondary },
  centered: { flex: 1, alignItems: 'center', justifyContent: 'center', padding: spacing.lg },
  erroTexto: { fontFamily: typography.fontFamily.body, fontSize: typography.fontSize.md, color: colors.error, textAlign: 'center', marginBottom: spacing.md },
  btnRetry: { backgroundColor: colors.primary, borderRadius: 8, paddingVertical: spacing.sm, paddingHorizontal: spacing.lg },
  btnRetryLabel: { fontFamily: typography.fontFamily.bodyBold, fontSize: typography.fontSize.md, color: colors.white },
});
