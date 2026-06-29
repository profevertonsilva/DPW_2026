import React, { useCallback, useEffect, useState } from 'react';
import {
  View, Text, ScrollView, StyleSheet, TouchableOpacity, ActivityIndicator, Image, Linking, Alert,
} from 'react-native';
import { useNavigation, useRoute } from '@react-navigation/native';
import type { NativeStackNavigationProp } from '@react-navigation/native-stack';
import type { RouteProp } from '@react-navigation/native';
import { avistamentoService } from '../../api/services/avistamentoService';
import { useAuth } from '../../hooks/useAuth';
import { colors } from '../../theme/colors';
import { typography } from '../../theme/typography';
import { spacing } from '../../theme/spacing';
import type { Avistamento } from '../../types/Avistamento';
import type { ResgatesStackParamList } from '../../navigation/stacks/ResgatesStack';

type Nav = NativeStackNavigationProp<ResgatesStackParamList, 'AvistamentoDetalhe'>;
type Route = RouteProp<ResgatesStackParamList, 'AvistamentoDetalhe'>;

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

function formatData(iso: string): string {
  const d = new Date(iso);
  return d.toLocaleString('pt-BR', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' });
}

export function AvistamentoDetalheScreen() {
  const { user } = useAuth();
  const route = useRoute<Route>();
  const navigation = useNavigation<Nav>();
  const { id } = route.params;

  const [item, setItem] = useState<Avistamento | null>(null);
  const [carregando, setCarregando] = useState(true);
  const [assumindo, setAssumindo] = useState(false);
  const [erro, setErro] = useState<string | null>(null);

  const carregar = useCallback(async () => {
    setCarregando(true);
    setErro(null);
    try {
      const dados = await avistamentoService.buscarPorId(id);
      setItem(dados);
    } catch (err: any) {
      setErro(err?.message ?? 'Avistamento não encontrado.');
    } finally {
      setCarregando(false);
    }
  }, [id]);

  useEffect(() => { carregar(); }, [carregar]);

  async function assumirAcolhimento() {
    // TODO: implementar lógica de transferência de animal para a ONG
    // Por ora: atualiza status para 'em_acolhimento' e mostra confirmação
    Alert.alert(
      'Assumir acolhimento',
      'Confirma que sua ONG irá acolher este animal?',
      [
        { text: 'Cancelar', style: 'cancel' },
        {
          text: 'Confirmar',
          onPress: async () => {
            setAssumindo(true);
            try {
              const atualizado = await avistamentoService.atualizarStatus(id, 'em_acolhimento');
              setItem(atualizado);
            } catch {
              Alert.alert('Erro', 'Não foi possível atualizar o status.');
            } finally {
              setAssumindo(false);
            }
          },
        },
      ],
    );
  }

  function verNoMapa() {
    if (!item) return;
    let url: string;
    if (item.lat && item.lng) {
      url = `https://maps.google.com/?q=${item.lat},${item.lng}`;
    } else if (item.endereco_texto) {
      url = `https://maps.google.com/?q=${encodeURIComponent(item.endereco_texto)}`;
    } else {
      Alert.alert('Localização', 'Nenhuma localização disponível para este avistamento.');
      return;
    }
    Linking.openURL(url).catch(() => Alert.alert('Erro', 'Não foi possível abrir o mapa.'));
  }

  if (carregando) {
    return <View style={styles.centered}><ActivityIndicator size="large" color={colors.primary} /></View>;
  }

  if (erro || !item) {
    return (
      <View style={styles.centered}>
        <Text style={styles.erroTexto}>{erro ?? 'Avistamento não encontrado.'}</Text>
        <TouchableOpacity style={styles.btnRetry} onPress={carregar}>
          <Text style={styles.btnRetryLabel}>Tentar novamente</Text>
        </TouchableOpacity>
      </View>
    );
  }

  const temLocalizacao = !!(item.lat || item.lng || item.endereco_texto);
  const isOng = user?.tipo_usuario === 'ong';
  const podeAssumir = isOng && item.status === 'aguardando_acolhimento';

  return (
    <ScrollView style={styles.root} contentContainerStyle={styles.scroll}>
      {item.foto ? (
        <Image source={{ uri: item.foto }} style={styles.foto} resizeMode="cover" />
      ) : (
        <View style={styles.fotoPlaceholder}>
          <Text style={styles.fotoPlaceholderIcon}>📷</Text>
          <Text style={styles.fotoPlaceholderText}>Sem foto</Text>
        </View>
      )}

      <View style={styles.content}>
        <View style={styles.headerRow}>
          <Text style={styles.especie}>{ESPECIE_LABEL[item.especie] ?? item.especie}</Text>
          <View style={[styles.statusBadge, { backgroundColor: STATUS_COLOR[item.status] ?? colors.border }]}>
            <Text style={styles.statusLabel}>{STATUS_LABEL[item.status] ?? item.status}</Text>
          </View>
        </View>

        <Text style={styles.sectionTitle}>Data do encontro</Text>
        <Text style={styles.body}>{formatData(item.data)}</Text>

        <Text style={styles.sectionTitle}>Reportado por</Text>
        <Text style={styles.body}>{item.usuario.nome}</Text>

        <Text style={styles.sectionTitle}>Condição física</Text>
        <Text style={styles.body}>{item.condicao}</Text>

        <Text style={styles.sectionTitle}>Ações tomadas</Text>
        <Text style={styles.body}>{item.acoes_tomadas}</Text>

        {temLocalizacao && (
          <>
            <Text style={styles.sectionTitle}>Localização</Text>
            {item.endereco_texto && <Text style={styles.body}>{item.endereco_texto}</Text>}
            {item.local_cidade && !item.endereco_texto && (
              <Text style={styles.body}>
                {[item.local_logradouro, item.local_numero, item.local_bairro, item.local_cidade, item.local_estado]
                  .filter(Boolean).join(', ')}
              </Text>
            )}
            {item.lat && item.lng && (
              <Text style={styles.coords}>
                Coord: {item.lat.toFixed(5)}, {item.lng.toFixed(5)}
              </Text>
            )}
            <TouchableOpacity style={styles.btnMapa} onPress={verNoMapa}>
              <Text style={styles.btnMapaLabel}>Ver no mapa →</Text>
            </TouchableOpacity>
          </>
        )}

        {podeAssumir && (
          <TouchableOpacity
            style={[styles.btnAssumir, assumindo && styles.btnDisabled]}
            onPress={assumirAcolhimento}
            disabled={assumindo}
            activeOpacity={0.85}>
            {assumindo
              ? <ActivityIndicator color={colors.white} />
              : <Text style={styles.btnAssumirLabel}>Assumir acolhimento</Text>}
          </TouchableOpacity>
        )}
      </View>
    </ScrollView>
  );
}

const styles = StyleSheet.create({
  root: { flex: 1, backgroundColor: colors.bgMuted },
  scroll: { paddingBottom: spacing.xxl },
  foto: { width: '100%', height: 240 },
  fotoPlaceholder: {
    height: 180, backgroundColor: colors.bgMuted,
    alignItems: 'center', justifyContent: 'center',
  },
  fotoPlaceholderIcon: { fontSize: 48, marginBottom: 4 },
  fotoPlaceholderText: { fontFamily: typography.fontFamily.body, fontSize: typography.fontSize.sm, color: colors.secondary },
  content: { padding: spacing.md, backgroundColor: colors.bg, borderRadius: 12, margin: spacing.md, elevation: 2, shadowColor: colors.black, shadowOffset: { width: 0, height: 2 }, shadowOpacity: 0.06, shadowRadius: 6 },
  headerRow: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', marginBottom: spacing.md },
  especie: { fontFamily: typography.fontFamily.titleBold, fontSize: typography.fontSize.xl, color: colors.text },
  statusBadge: { borderRadius: 12, paddingVertical: 4, paddingHorizontal: 10 },
  statusLabel: { fontFamily: typography.fontFamily.bodyBold, fontSize: typography.fontSize.xs, color: colors.white },
  sectionTitle: {
    fontFamily: typography.fontFamily.bodyBold, fontSize: typography.fontSize.sm,
    color: colors.secondary, textTransform: 'uppercase', letterSpacing: 0.5,
    marginTop: spacing.md, marginBottom: 4,
  },
  body: { fontFamily: typography.fontFamily.body, fontSize: typography.fontSize.md, color: colors.text, lineHeight: 22 },
  coords: { fontFamily: typography.fontFamily.body, fontSize: typography.fontSize.xs, color: colors.secondary, marginTop: 2 },
  btnMapa: { marginTop: spacing.sm },
  btnMapaLabel: { fontFamily: typography.fontFamily.bodyBold, fontSize: typography.fontSize.md, color: colors.primary },
  btnAssumir: {
    marginTop: spacing.lg, backgroundColor: colors.success, borderRadius: 10,
    paddingVertical: spacing.md, alignItems: 'center',
  },
  btnAssumirLabel: { fontFamily: typography.fontFamily.bodyBold, fontSize: typography.fontSize.md, color: colors.white },
  btnDisabled: { backgroundColor: colors.border },
  centered: { flex: 1, alignItems: 'center', justifyContent: 'center', padding: spacing.lg },
  erroTexto: { fontFamily: typography.fontFamily.body, fontSize: typography.fontSize.md, color: colors.error, textAlign: 'center', marginBottom: spacing.md },
  btnRetry: { backgroundColor: colors.primary, borderRadius: 8, paddingVertical: spacing.sm, paddingHorizontal: spacing.lg },
  btnRetryLabel: { fontFamily: typography.fontFamily.bodyBold, fontSize: typography.fontSize.md, color: colors.white },
});
