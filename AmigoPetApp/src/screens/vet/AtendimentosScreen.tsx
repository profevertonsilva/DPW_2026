import React, { useCallback, useEffect, useState } from 'react';
import {
  View, Text, FlatList, StyleSheet, TouchableOpacity, ActivityIndicator, RefreshControl,
} from 'react-native';
import { useNavigation } from '@react-navigation/native';
import type { NativeStackNavigationProp } from '@react-navigation/native-stack';
import { vetService } from '../../api/services/vetService';
import { EmptyState } from '../../components/ui/EmptyState';
import { colors } from '../../theme/colors';
import { typography } from '../../theme/typography';
import { spacing } from '../../theme/spacing';
import type { AtendimentoResumo } from '../../types/Saude';
import type { AtendimentosStackParamList } from '../../navigation/stacks/AtendimentosStack';

type Nav = NativeStackNavigationProp<AtendimentosStackParamList, 'Atendimentos'>;

function formatData(iso: string | null): string {
  if (!iso) return '—';
  return new Date(iso).toLocaleDateString('pt-BR');
}

function AnimalCard({ item, onVacina, onProcedimento }: {
  item: AtendimentoResumo;
  onVacina: () => void;
  onProcedimento: () => void;
}) {
  return (
    <View style={styles.card}>
      <View style={styles.cardInfo}>
        <Text style={styles.cardNome}>{item.animal_nome}</Text>
        <Text style={styles.cardEspecie}>{item.animal_especie ?? 'Espécie não informada'}</Text>
        <View style={styles.cardDatas}>
          <Text style={styles.cardData}>💉 Última vacina: {formatData(item.ultima_vacina)}</Text>
          <Text style={styles.cardData}>🩺 Último proc.: {formatData(item.ultimo_procedimento)}</Text>
        </View>
      </View>
      <View style={styles.cardAcoes}>
        <TouchableOpacity style={styles.btnAcao} onPress={onVacina}>
          <Text style={styles.btnAcaoLabel}>+ Vacina</Text>
        </TouchableOpacity>
        <TouchableOpacity style={[styles.btnAcao, styles.btnAcaoProc]} onPress={onProcedimento}>
          <Text style={[styles.btnAcaoLabel, styles.btnAcaoProcLabel]}>+ Proc.</Text>
        </TouchableOpacity>
      </View>
    </View>
  );
}

export function AtendimentosScreen() {
  const navigation = useNavigation<Nav>();
  const [atendimentos, setAtendimentos] = useState<AtendimentoResumo[]>([]);
  const [carregando, setCarregando] = useState(true);
  const [atualizando, setAtualizando] = useState(false);
  const [erro, setErro] = useState<string | null>(null);

  const carregar = useCallback(async (refresh = false) => {
    if (refresh) setAtualizando(true);
    else setCarregando(true);
    setErro(null);
    try {
      setAtendimentos(await vetService.meusAtendimentos());
    } catch (err: any) {
      setErro(err?.message ?? 'Erro ao carregar atendimentos.');
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
        data={atendimentos}
        keyExtractor={item => String(item.animal_id)}
        renderItem={({ item }) => (
          <AnimalCard
            item={item}
            onVacina={() => navigation.navigate('AdicionarVacinaVet', { animalId: item.animal_id })}
            onProcedimento={() => navigation.navigate('AdicionarProcedimentoVet', { animalId: item.animal_id })}
          />
        )}
        contentContainerStyle={styles.lista}
        refreshControl={<RefreshControl refreshing={atualizando} onRefresh={() => carregar(true)} colors={[colors.primary]} />}
        ListEmptyComponent={
          <EmptyState
            icon="🩺"
            title="Nenhum animal sob seus cuidados"
            message="Animais que você atender aparecerão aqui."
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
    flexDirection: 'row', alignItems: 'center', backgroundColor: colors.bg, borderRadius: 12,
    padding: spacing.md, marginBottom: spacing.sm,
    elevation: 1, shadowColor: colors.black, shadowOffset: { width: 0, height: 1 }, shadowOpacity: 0.06, shadowRadius: 3,
  },
  cardInfo: { flex: 1 },
  cardNome: { fontFamily: typography.fontFamily.titleBold, fontSize: typography.fontSize.md, color: colors.text },
  cardEspecie: { fontFamily: typography.fontFamily.body, fontSize: typography.fontSize.sm, color: colors.secondary, marginTop: 2 },
  cardDatas: { marginTop: spacing.xs },
  cardData: { fontFamily: typography.fontFamily.body, fontSize: typography.fontSize.xs, color: colors.secondary },
  cardAcoes: { gap: spacing.xs },
  btnAcao: {
    backgroundColor: colors.primary, borderRadius: 8,
    paddingVertical: 6, paddingHorizontal: spacing.sm, alignItems: 'center',
  },
  btnAcaoProc: { backgroundColor: colors.info },
  btnAcaoLabel: { fontFamily: typography.fontFamily.bodyBold, fontSize: typography.fontSize.xs, color: colors.white },
  btnAcaoProcLabel: { color: colors.white },
  centered: { flex: 1, alignItems: 'center', justifyContent: 'center', padding: spacing.lg },
  erroTexto: { fontFamily: typography.fontFamily.body, fontSize: typography.fontSize.md, color: colors.error, textAlign: 'center', marginBottom: spacing.md },
  btnRetry: { backgroundColor: colors.primary, borderRadius: 8, paddingVertical: spacing.sm, paddingHorizontal: spacing.lg },
  btnRetryLabel: { fontFamily: typography.fontFamily.bodyBold, fontSize: typography.fontSize.md, color: colors.white },
});
