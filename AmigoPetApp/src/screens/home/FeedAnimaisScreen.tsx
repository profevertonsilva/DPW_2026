import React, { useCallback, useEffect, useState } from 'react';
import {
  FlatList,
  View,
  Text,
  StyleSheet,
  TextInput,
  ScrollView,
  RefreshControl,
  ActivityIndicator,
} from 'react-native';
import { NativeStackScreenProps } from '@react-navigation/native-stack';
import { HomeStackParamList } from '../../navigation/stacks/HomeStack';
import { AnimalCard } from '../../components/domain/AnimalCard';
import { EmptyState } from '../../components/ui/EmptyState';
import { Chip } from '../../components/ui/Chip';
import { animalService } from '../../api/services/animalService';
import { Animal, AnimalPorte, AnimalSexo, FiltrosAnimal } from '../../types/Animal';
import { colors } from '../../theme/colors';
import { typography } from '../../theme/typography';
import { spacing } from '../../theme/spacing';

type Props = NativeStackScreenProps<HomeStackParamList, 'FeedAnimais'>;

const ESPECIE_CHIPS = ['Todos', 'Cachorro', 'Gato', 'Outro'];
const PORTE_MAP: Record<string, AnimalPorte> = { Pequeno: 'pequeno', Médio: 'medio', Grande: 'grande' };
const SEXO_MAP: Record<string, AnimalSexo> = { Macho: 'm', Fêmea: 'f' };

export function FeedAnimaisScreen({ navigation }: Props) {
  const [animais, setAnimais] = useState<Animal[]>([]);
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);
  const [busca, setBusca] = useState('');
  const [especieSel, setEspecieSel] = useState('Todos');
  const [porteSel, setPorteSel] = useState('');
  const [sexoSel, setSexoSel] = useState('');

  const carregar = useCallback(async (isRefresh = false) => {
    if (isRefresh) setRefreshing(true); else setLoading(true);
    try {
      const filtros: FiltrosAnimal = {};
      if (especieSel !== 'Todos') filtros.especie = especieSel.toLowerCase();
      if (porteSel) filtros.porte = PORTE_MAP[porteSel];
      if (sexoSel) filtros.sexo = SEXO_MAP[sexoSel];
      if (busca.trim()) filtros.busca = busca.trim();
      const lista = await animalService.listar(filtros);
      setAnimais(lista);
    } finally {
      if (isRefresh) setRefreshing(false); else setLoading(false);
    }
  }, [especieSel, porteSel, sexoSel, busca]);

  useEffect(() => { carregar(); }, [carregar]);

  if (loading) {
    return (
      <View style={styles.center}>
        <ActivityIndicator size="large" color={colors.primary} />
      </View>
    );
  }

  return (
    <View style={styles.container}>
      <View style={styles.searchBar}>
        <View style={styles.searchInputWrap}>
          <Text style={styles.searchIcon}>🔍</Text>
          <TextInput
            style={styles.input}
            placeholder="Buscar por nome..."
            placeholderTextColor={colors.secondary}
            value={busca}
            onChangeText={setBusca}
            onSubmitEditing={() => carregar()}
            returnKeyType="search"
          />
        </View>
      </View>

      <ScrollView
        horizontal
        showsHorizontalScrollIndicator={false}
        contentContainerStyle={styles.chips}
      >
        {ESPECIE_CHIPS.map(e => (
          <Chip key={e} label={e} active={especieSel === e} onPress={() => setEspecieSel(e)} />
        ))}
        {Object.keys(PORTE_MAP).map(p => (
          <Chip key={p} label={p} active={porteSel === p} onPress={() => setPorteSel(porteSel === p ? '' : p)} />
        ))}
        {Object.keys(SEXO_MAP).map(s => (
          <Chip key={s} label={s} active={sexoSel === s} onPress={() => setSexoSel(sexoSel === s ? '' : s)} />
        ))}
      </ScrollView>

      <FlatList
        data={animais}
        keyExtractor={a => String(a.id)}
        renderItem={({ item }) => (
          <AnimalCard animal={item} onPress={() => navigation.navigate('AnimalDetalhe', { id: item.id })} />
        )}
        contentContainerStyle={styles.lista}
        refreshControl={
          <RefreshControl refreshing={refreshing} onRefresh={() => carregar(true)} colors={[colors.primary]} />
        }
        ListEmptyComponent={
          <EmptyState
            icon="🐾"
            title="Nenhum animal encontrado"
            message="Tente ajustar os filtros de busca."
          />
        }
      />
    </View>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: colors.bgMuted },
  center: { flex: 1, justifyContent: 'center', alignItems: 'center' },
  searchBar: {
    backgroundColor: colors.bg,
    paddingHorizontal: spacing.md,
    paddingVertical: spacing.sm,
    borderBottomWidth: 1,
    borderBottomColor: colors.border,
  },
  searchInputWrap: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: colors.bgMuted,
    borderRadius: 8,
    paddingHorizontal: spacing.md,
  },
  searchIcon: {
    fontSize: typography.fontSize.md,
    marginRight: spacing.sm,
  },
  input: {
    flex: 1,
    paddingVertical: spacing.sm,
    fontFamily: typography.fontFamily.body,
    fontSize: typography.fontSize.md,
    color: colors.text,
  },
  chips: {
    paddingHorizontal: spacing.md,
    paddingVertical: spacing.sm,
    backgroundColor: colors.bg,
  },
  lista: { padding: spacing.md, flexGrow: 1 },
});
