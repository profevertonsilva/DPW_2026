import React, { useEffect, useState } from 'react';
import { View, FlatList, StyleSheet, ActivityIndicator, RefreshControl } from 'react-native';
import { NativeStackScreenProps } from '@react-navigation/native-stack';
import { useNavigation } from '@react-navigation/native';
import { PerfilStackParamList } from '../../navigation/stacks/PerfilStack';
import { NotificacaoItem } from '../../components/domain/NotificacaoItem';
import { EmptyState } from '../../components/ui/EmptyState';
import { notificacaoService } from '../../api/services/notificacaoService';
import { Notificacao } from '../../types/Notificacao';
import { colors } from '../../theme/colors';

type Props = NativeStackScreenProps<PerfilStackParamList, 'Notificacoes'>;

export function NotificacoesScreen(_: Props) {
  const navigation = useNavigation<any>();
  const [notificacoes, setNotificacoes] = useState<Notificacao[]>([]);
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);

  async function carregar(isRefresh = false) {
    if (isRefresh) setRefreshing(true); else setLoading(true);
    try { setNotificacoes(await notificacaoService.listar()); }
    finally { if (isRefresh) setRefreshing(false); else setLoading(false); }
  }

  useEffect(() => { carregar(); }, []);

  async function handlePress(item: Notificacao) {
    if (!item.lida) {
      await notificacaoService.marcarLida(item.id);
      setNotificacoes(ns => ns.map(n => n.id === item.id ? { ...n, lida: true } : n));
    }
    if (item.destino) {
      try {
        navigation.getParent()?.navigate(item.destino.tab, {
          screen: item.destino.tela,
          params: item.destino.params,
        });
      } catch { /* destino pode não estar disponível para o papel atual */ }
    }
  }

  if (loading) {
    return <View style={styles.center}><ActivityIndicator size="large" color={colors.primary} /></View>;
  }

  return (
    <FlatList
      data={notificacoes}
      keyExtractor={n => String(n.id)}
      renderItem={({ item }) => (
        <NotificacaoItem
          notificacao={item}
          onPress={() => handlePress(item)}
        />
      )}
      style={styles.root}
      refreshControl={
        <RefreshControl refreshing={refreshing} onRefresh={() => carregar(true)} colors={[colors.primary]} />
      }
      ListEmptyComponent={
        <EmptyState icon="🔔" title="Sem notificações" message="Você está em dia!" />
      }
    />
  );
}

const styles = StyleSheet.create({
  root: { flex: 1, backgroundColor: colors.bgMuted },
  center: { flex: 1, justifyContent: 'center', alignItems: 'center' },
});
