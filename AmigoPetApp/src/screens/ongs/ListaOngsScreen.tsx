import React, { useEffect, useState } from 'react';
import { View, Text, FlatList, StyleSheet, TextInput, RefreshControl, ActivityIndicator } from 'react-native';
import { NativeStackScreenProps } from '@react-navigation/native-stack';
import { OngsStackParamList } from '../../navigation/stacks/OngsStack';
import { OngCard } from '../../components/domain/OngCard';
import { EmptyState } from '../../components/ui/EmptyState';
import { ongService } from '../../api/services/ongService';
import { Ong } from '../../types/Ong';
import { colors } from '../../theme/colors';
import { typography } from '../../theme/typography';
import { spacing } from '../../theme/spacing';

type Props = NativeStackScreenProps<OngsStackParamList, 'ListaOngs'>;

export function ListaOngsScreen({ navigation }: Props) {
  const [ongs, setOngs] = useState<Ong[]>([]);
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);
  const [busca, setBusca] = useState('');

  async function carregar(isRefresh = false) {
    if (isRefresh) setRefreshing(true); else setLoading(true);
    try { setOngs(await ongService.listar()); }
    finally { if (isRefresh) setRefreshing(false); else setLoading(false); }
  }

  useEffect(() => { carregar(); }, []);

  const dados = busca.trim()
    ? ongs.filter(o => o.nome.toLowerCase().includes(busca.toLowerCase()))
    : ongs;

  if (loading) {
    return <View style={styles.center}><ActivityIndicator size="large" color={colors.primary} /></View>;
  }

  return (
    <View style={styles.root}>
      <View style={styles.searchBar}>
        <View style={styles.searchInputWrap}>
          <Text style={styles.searchIcon}>🔍</Text>
          <TextInput
            style={styles.input}
            placeholder="Buscar ONG..."
            placeholderTextColor={colors.secondary}
            value={busca}
            onChangeText={setBusca}
          />
        </View>
      </View>
      <FlatList
        data={dados}
        keyExtractor={o => String(o.id)}
        renderItem={({ item }) => (
          <OngCard ong={item} onPress={() => navigation.navigate('OngDetalhe', { id: item.id })} />
        )}
        contentContainerStyle={styles.lista}
        refreshControl={
          <RefreshControl refreshing={refreshing} onRefresh={() => carregar(true)} colors={[colors.primary]} />
        }
        ListEmptyComponent={
          <EmptyState icon="🏠" title="Nenhuma ONG encontrada" message="Tente ajustar a busca." />
        }
      />
    </View>
  );
}

const styles = StyleSheet.create({
  root: { flex: 1, backgroundColor: colors.bgMuted },
  center: { flex: 1, justifyContent: 'center', alignItems: 'center' },
  searchBar: { backgroundColor: colors.bg, paddingHorizontal: spacing.md, paddingVertical: spacing.sm, borderBottomWidth: 1, borderBottomColor: colors.border },
  searchInputWrap: { flexDirection: 'row', alignItems: 'center', backgroundColor: colors.bgMuted, borderRadius: 8, paddingHorizontal: spacing.md },
  searchIcon: { fontSize: typography.fontSize.md, marginRight: spacing.xs },
  input: {
    flex: 1,
    paddingVertical: spacing.sm,
    fontFamily: typography.fontFamily.body,
    fontSize: typography.fontSize.md,
    color: colors.text,
  },
  lista: { padding: spacing.md, flexGrow: 1 },
});
