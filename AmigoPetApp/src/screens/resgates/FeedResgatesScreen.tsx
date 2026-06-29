import React, { useCallback, useEffect, useState } from 'react';
import {
  View, Text, FlatList, StyleSheet, TouchableOpacity, ActivityIndicator, RefreshControl, Image,
} from 'react-native';
import { useNavigation } from '@react-navigation/native';
import type { NativeStackNavigationProp } from '@react-navigation/native-stack';
import type { CompositeNavigationProp } from '@react-navigation/native';
import type { BottomTabNavigationProp } from '@react-navigation/bottom-tabs';
import { avistamentoService } from '../../api/services/avistamentoService';
import { EmptyState } from '../../components/ui/EmptyState';
import { colors } from '../../theme/colors';
import { typography } from '../../theme/typography';
import { spacing } from '../../theme/spacing';
import type { Avistamento } from '../../types/Avistamento';
import type { ResgatesStackParamList } from '../../navigation/stacks/ResgatesStack';
import type { AppStackParamList } from '../../navigation/RootNavigator';

type Nav = NativeStackNavigationProp<ResgatesStackParamList, 'FeedResgates'>;

const ESPECIE_LABEL: Record<string, string> = {
  cachorro: 'Cachorro',
  gato: 'Gato',
  outro: 'Outro',
  nao_sei: 'Espécie desconhecida',
};

const STATUS_LABEL: Record<string, string> = {
  aguardando_acolhimento: 'Aguardando acolhimento',
  em_acolhimento: 'Em acolhimento',
  resgatado: 'Resgatado',
  encerrado: 'Encerrado',
};

const STATUS_COLOR: Record<string, string> = {
  aguardando_acolhimento: colors.error,
  em_acolhimento: colors.accent,
  resgatado: colors.success,
  encerrado: colors.secondary,
};

function tempoRelativo(iso: string): string {
  const diff = (Date.now() - new Date(iso).getTime()) / 1000;
  if (diff < 60) return 'Agora mesmo';
  if (diff < 3600) return `${Math.floor(diff / 60)}min atrás`;
  if (diff < 86400) return `${Math.floor(diff / 3600)}h atrás`;
  return `${Math.floor(diff / 86400)}d atrás`;
}

function AvistamentoCard({ item, onPress }: { item: Avistamento; onPress: () => void }) {
  const local = [item.local_bairro, item.local_cidade, item.local_estado].filter(Boolean).join(', ');

  return (
    <TouchableOpacity style={styles.card} onPress={onPress} activeOpacity={0.85}>
      {item.foto ? (
        <Image source={{ uri: item.foto }} style={styles.foto} />
      ) : (
        <View style={styles.fotoPlaceholder}>
          <Text style={styles.fotoPlaceholderText}>📷</Text>
        </View>
      )}
      <View style={styles.cardBody}>
        <View style={styles.cardHeader}>
          <Text style={styles.especie}>{ESPECIE_LABEL[item.especie] ?? item.especie}</Text>
          <View style={[styles.statusBadge, { backgroundColor: STATUS_COLOR[item.status] ?? colors.border }]}>
            <Text style={styles.statusLabel}>{STATUS_LABEL[item.status] ?? item.status}</Text>
          </View>
        </View>
        {local ? <Text style={styles.local} numberOfLines={1}>📍 {local}</Text> : null}
        <Text style={styles.condicao} numberOfLines={2}>{item.condicao}</Text>
        <View style={styles.cardFooter}>
          <Text style={styles.usuario}>{item.usuario.nome}</Text>
          <Text style={styles.tempo}>{tempoRelativo(item.data)}</Text>
        </View>
      </View>
    </TouchableOpacity>
  );
}

export function FeedResgatesScreen() {
  const navigation = useNavigation<Nav>();
  const rootNav = useNavigation<NativeStackNavigationProp<AppStackParamList>>();
  const [avistamentos, setAvistamentos] = useState<Avistamento[]>([]);
  const [carregando, setCarregando] = useState(true);
  const [atualizando, setAtualizando] = useState(false);
  const [erro, setErro] = useState<string | null>(null);

  const carregar = useCallback(async (refresh = false) => {
    if (refresh) setAtualizando(true);
    else setCarregando(true);
    setErro(null);
    try {
      const dados = await avistamentoService.listar();
      setAvistamentos(dados);
    } catch (err: any) {
      setErro(err?.message ?? 'Erro ao carregar resgates.');
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
        data={avistamentos}
        keyExtractor={item => String(item.id)}
        renderItem={({ item }) => (
          <AvistamentoCard
            item={item}
            onPress={() => navigation.navigate('AvistamentoDetalhe', { id: item.id })}
          />
        )}
        contentContainerStyle={styles.lista}
        refreshControl={<RefreshControl refreshing={atualizando} onRefresh={() => carregar(true)} colors={[colors.primary]} />}
        ListHeaderComponent={
          <View style={styles.listHeader}>
            <Text style={styles.listHeaderTitle}>Animais em situação de rua</Text>
            <TouchableOpacity onPress={() => navigation.navigate('Ranking')}>
              <Text style={styles.rankingLink}>🏆 Ver ranking</Text>
            </TouchableOpacity>
          </View>
        }
        ListEmptyComponent={
          <EmptyState
            icon="🐾"
            title="Nenhum avistamento registrado"
            message="Quando alguém reportar um animal de rua, aparecerá aqui."
          />
        }
      />
    </View>
  );
}

const styles = StyleSheet.create({
  root: { flex: 1, backgroundColor: colors.bgMuted },
  lista: { padding: spacing.md, paddingBottom: 90 },
  listHeader: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', marginBottom: spacing.sm },
  listHeaderTitle: { fontFamily: typography.fontFamily.titleBold, fontSize: typography.fontSize.md, color: colors.text },
  rankingLink: { fontFamily: typography.fontFamily.bodyBold, fontSize: typography.fontSize.sm, color: colors.primary },
  card: {
    flexDirection: 'row', backgroundColor: colors.bg, borderRadius: 12,
    marginBottom: spacing.sm, overflow: 'hidden',
    elevation: 1, shadowColor: colors.black, shadowOffset: { width: 0, height: 1 }, shadowOpacity: 0.06, shadowRadius: 3,
  },
  foto: { width: 90, height: 100 },
  fotoPlaceholder: { width: 90, height: 100, backgroundColor: colors.bgMuted, alignItems: 'center', justifyContent: 'center' },
  fotoPlaceholderText: { fontSize: 28 },
  cardBody: { flex: 1, padding: spacing.sm },
  cardHeader: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'flex-start', marginBottom: 4 },
  especie: { fontFamily: typography.fontFamily.bodyBold, fontSize: typography.fontSize.md, color: colors.text, flex: 1, marginRight: 4 },
  statusBadge: { borderRadius: 8, paddingVertical: 2, paddingHorizontal: 6 },
  statusLabel: { fontFamily: typography.fontFamily.bodyBold, fontSize: typography.fontSize.xs, color: colors.white },
  local: { fontFamily: typography.fontFamily.body, fontSize: typography.fontSize.xs, color: colors.secondary, marginBottom: 2 },
  condicao: { fontFamily: typography.fontFamily.body, fontSize: typography.fontSize.sm, color: colors.text, marginBottom: spacing.xs },
  cardFooter: { flexDirection: 'row', justifyContent: 'space-between' },
  usuario: { fontFamily: typography.fontFamily.body, fontSize: typography.fontSize.xs, color: colors.secondary },
  tempo: { fontFamily: typography.fontFamily.body, fontSize: typography.fontSize.xs, color: colors.secondary },
  centered: { flex: 1, alignItems: 'center', justifyContent: 'center', padding: spacing.lg },
  erroTexto: { fontFamily: typography.fontFamily.body, fontSize: typography.fontSize.md, color: colors.error, textAlign: 'center', marginBottom: spacing.md },
  btnRetry: { backgroundColor: colors.primary, borderRadius: 8, paddingVertical: spacing.sm, paddingHorizontal: spacing.lg },
  btnRetryLabel: { fontFamily: typography.fontFamily.bodyBold, fontSize: typography.fontSize.md, color: colors.white },
});
