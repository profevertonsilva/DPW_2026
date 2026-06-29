import React, { useCallback, useEffect, useState } from 'react';
import {
  View, Text, FlatList, StyleSheet, TouchableOpacity, ActivityIndicator, RefreshControl,
} from 'react-native';
import { useNavigation } from '@react-navigation/native';
import type { NativeStackNavigationProp } from '@react-navigation/native-stack';
import { animalService } from '../../api/services/animalService';
import { EmptyState } from '../../components/ui/EmptyState';
import { colors } from '../../theme/colors';
import { typography } from '../../theme/typography';
import { spacing } from '../../theme/spacing';
import type { Animal } from '../../types/Animal';
import type { MeusAnimaisStackParamList } from '../../navigation/stacks/MeusAnimaisStack';

type Nav = NativeStackNavigationProp<MeusAnimaisStackParamList, 'MeusAnimais'>;

const STATUS_LABEL: Record<string, string> = {
  disponivel: 'Disponível',
  adotado: 'Adotado',
  em_tratamento: 'Em tratamento',
  reservado: 'Reservado',
};

const STATUS_COLOR: Record<string, string> = {
  disponivel: colors.success,
  adotado: colors.secondary,
  em_tratamento: colors.accent,
  reservado: colors.info,
};

function AnimalItem({ animal }: { animal: Animal }) {
  return (
    <View style={styles.card}>
      <View style={styles.cardInfo}>
        <Text style={styles.cardNome}>{animal.nome}</Text>
        <Text style={styles.cardSub}>
          {[animal.especie, animal.raca].filter(Boolean).join(' · ')}
        </Text>
        <View style={[styles.statusBadge, { backgroundColor: STATUS_COLOR[animal.status] ?? colors.border }]}>
          <Text style={styles.statusLabel}>{STATUS_LABEL[animal.status] ?? animal.status}</Text>
        </View>
      </View>
    </View>
  );
}

export function MeusAnimaisScreen() {
  const navigation = useNavigation<Nav>();
  const [animais, setAnimais] = useState<Animal[]>([]);
  const [carregando, setCarregando] = useState(true);
  const [atualizando, setAtualizando] = useState(false);
  const [erro, setErro] = useState<string | null>(null);

  const carregar = useCallback(async (refresh = false) => {
    if (refresh) setAtualizando(true);
    else setCarregando(true);
    setErro(null);
    try {
      const dados = await animalService.meus();
      setAnimais(dados);
    } catch (err: any) {
      setErro(err?.message ?? 'Erro ao carregar animais.');
    } finally {
      setCarregando(false);
      setAtualizando(false);
    }
  }, []);

  useEffect(() => { carregar(); }, [carregar]);

  if (carregando) {
    return (
      <View style={styles.centered}>
        <ActivityIndicator size="large" color={colors.primary} />
      </View>
    );
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
        data={animais}
        keyExtractor={item => String(item.id)}
        renderItem={({ item }) => <AnimalItem animal={item} />}
        contentContainerStyle={styles.lista}
        refreshControl={<RefreshControl refreshing={atualizando} onRefresh={() => carregar(true)} colors={[colors.primary]} />}
        ListEmptyComponent={
          <EmptyState
            icon="🐾"
            title="Nenhum animal cadastrado"
            message="Cadastre seu primeiro pet para começar."
          />
        }
      />

      <TouchableOpacity
        style={styles.btnCadastrar}
        onPress={() => navigation.navigate('CadastrarAnimal')}
        activeOpacity={0.85}
      >
        <Text style={styles.btnCadastrarLabel}>+ Cadastrar novo pet</Text>
      </TouchableOpacity>
    </View>
  );
}

const styles = StyleSheet.create({
  root: { flex: 1, backgroundColor: colors.bgMuted },
  lista: { padding: spacing.md, paddingBottom: 90 },
  card: {
    backgroundColor: colors.bg, borderRadius: 12, padding: spacing.md,
    marginBottom: spacing.sm, elevation: 2,
    shadowColor: colors.black, shadowOffset: { width: 0, height: 2 }, shadowOpacity: 0.06, shadowRadius: 6,
  },
  cardInfo: { flex: 1 },
  cardNome: { fontFamily: typography.fontFamily.titleBold, fontSize: typography.fontSize.md, color: colors.text },
  cardSub: { fontFamily: typography.fontFamily.body, fontSize: typography.fontSize.sm, color: colors.secondary, marginTop: 2 },
  statusBadge: { alignSelf: 'flex-start', borderRadius: 12, paddingVertical: 2, paddingHorizontal: spacing.sm, marginTop: spacing.xs },
  statusLabel: { fontFamily: typography.fontFamily.bodyBold, fontSize: typography.fontSize.xs, color: colors.white },
  btnCadastrar: {
    position: 'absolute', bottom: spacing.lg, left: spacing.lg, right: spacing.lg,
    backgroundColor: colors.primary, borderRadius: 10,
    paddingVertical: spacing.md, alignItems: 'center',
    elevation: 4, shadowColor: colors.black, shadowOffset: { width: 0, height: 2 }, shadowOpacity: 0.15, shadowRadius: 6,
  },
  btnCadastrarLabel: { fontFamily: typography.fontFamily.bodyBold, fontSize: typography.fontSize.md, color: colors.white },
  centered: { flex: 1, alignItems: 'center', justifyContent: 'center', padding: spacing.lg },
  erroTexto: { fontFamily: typography.fontFamily.body, fontSize: typography.fontSize.md, color: colors.error, textAlign: 'center', marginBottom: spacing.md },
  btnRetry: { backgroundColor: colors.primary, borderRadius: 8, paddingVertical: spacing.sm, paddingHorizontal: spacing.lg },
  btnRetryLabel: { fontFamily: typography.fontFamily.bodyBold, fontSize: typography.fontSize.md, color: colors.white },
});
