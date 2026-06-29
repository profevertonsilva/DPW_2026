import React, { useCallback, useEffect, useState } from 'react';
import {
  View, Text, ScrollView, StyleSheet, TouchableOpacity, ActivityIndicator, Image, Linking, Alert,
} from 'react-native';
import { useRoute } from '@react-navigation/native';
import type { RouteProp } from '@react-navigation/native';
import { carteiraService } from '../../api/services/carteiraService';
import { colors } from '../../theme/colors';
import { typography } from '../../theme/typography';
import { spacing } from '../../theme/spacing';
import type { CarteiraIdentificacao } from '../../types/Saude';

export function CarteiraIdentificacaoScreen() {
  const route = useRoute<RouteProp<{ p: { animalId: number } }, 'p'>>();
  const animalId = (route.params as any)?.animalId as number;

  const [carteira, setCarteira] = useState<CarteiraIdentificacao | null>(null);
  const [carregando, setCarregando] = useState(true);
  const [erro, setErro] = useState<string | null>(null);

  const carregar = useCallback(async () => {
    setCarregando(true);
    setErro(null);
    try {
      setCarteira(await carteiraService.obter(animalId));
    } catch (err: any) {
      setErro(err?.message ?? 'Erro ao carregar carteira.');
    } finally { setCarregando(false); }
  }, [animalId]);

  useEffect(() => { carregar(); }, [carregar]);

  function calcularIdade(dataNasc: string | null): string {
    if (!dataNasc) return '—';
    const anos = Math.floor((Date.now() - new Date(dataNasc).getTime()) / (1000 * 60 * 60 * 24 * 365.25));
    if (anos === 0) {
      const meses = Math.floor((Date.now() - new Date(dataNasc).getTime()) / (1000 * 60 * 60 * 24 * 30));
      return `${meses} ${meses === 1 ? 'mês' : 'meses'}`;
    }
    return `${anos} ${anos === 1 ? 'ano' : 'anos'}`;
  }

  async function abrirPDF() {
    if (!carteira?.pdf_url) return;
    try {
      const suportado = await Linking.canOpenURL(carteira.pdf_url);
      if (suportado) {
        await Linking.openURL(carteira.pdf_url);
      } else {
        Alert.alert('Erro', 'Não foi possível abrir o PDF.');
      }
    } catch {
      Alert.alert('Erro', 'Não foi possível abrir o PDF.');
    }
  }

  if (carregando) {
    return <View style={styles.centered}><ActivityIndicator size="large" color={colors.primary} /></View>;
  }

  if (erro || !carteira) {
    return (
      <View style={styles.centered}>
        <Text style={styles.erroTexto}>{erro ?? 'Carteira não encontrada.'}</Text>
        <TouchableOpacity style={styles.btnRetry} onPress={carregar}>
          <Text style={styles.btnRetryLabel}>Tentar novamente</Text>
        </TouchableOpacity>
      </View>
    );
  }

  return (
    <ScrollView style={styles.root} contentContainerStyle={styles.scroll}>
      <View style={styles.card}>
        {carteira.foto ? (
          <Image source={{ uri: carteira.foto }} style={styles.foto} />
        ) : (
          <View style={styles.fotoPlaceholder}><Text style={styles.fotoPlaceholderIcon}>🐾</Text></View>
        )}

        <Text style={styles.nome}>{carteira.nome}</Text>
        {carteira.raca && <Text style={styles.raca}>{carteira.raca}</Text>}

        <View style={styles.grid}>
          <InfoRow label="Espécie" value={carteira.especie ?? '—'} />
          <InfoRow label="Nascimento" value={carteira.data_nascimento ? new Date(carteira.data_nascimento).toLocaleDateString('pt-BR') : '—'} />
          <InfoRow label="Idade" value={calcularIdade(carteira.data_nascimento)} />
          <InfoRow label="Castrado" value={carteira.castrado ? 'Sim' : 'Não'} />
          {carteira.alergias && <InfoRow label="Alergias" value={carteira.alergias} />}
        </View>
      </View>

      <View style={styles.qrCard}>
        <Text style={styles.qrTitulo}>QR Code de Identificação</Text>
        {carteira.qr_code_url ? (
          <Image
            source={{ uri: carteira.qr_code_url }}
            style={styles.qr}
            resizeMode="contain"
          />
        ) : (
          <Text style={styles.qrSub}>QR Code não disponível</Text>
        )}
        <Text style={styles.qrSub}>Escaneie para ver o perfil completo do animal</Text>
      </View>

      <TouchableOpacity style={styles.btnPdf} onPress={abrirPDF} activeOpacity={0.85}>
        <Text style={styles.btnPdfLabel}>📄 Baixar Carteira em PDF</Text>
      </TouchableOpacity>
    </ScrollView>
  );
}

function InfoRow({ label, value }: { label: string; value: string }) {
  return (
    <View style={styles.infoRow}>
      <Text style={styles.infoLabel}>{label}</Text>
      <Text style={styles.infoValue}>{value}</Text>
    </View>
  );
}

const styles = StyleSheet.create({
  root: { flex: 1, backgroundColor: colors.bgMuted },
  scroll: { padding: spacing.md, paddingBottom: spacing.xxl },
  card: {
    backgroundColor: colors.bg, borderRadius: 16, padding: spacing.lg,
    alignItems: 'center', marginBottom: spacing.md,
    elevation: 2, shadowColor: colors.black, shadowOffset: { width: 0, height: 2 }, shadowOpacity: 0.08, shadowRadius: 6,
  },
  foto: { width: 100, height: 100, borderRadius: 50, marginBottom: spacing.sm },
  fotoPlaceholder: {
    width: 100, height: 100, borderRadius: 50, backgroundColor: colors.bgMuted,
    alignItems: 'center', justifyContent: 'center', marginBottom: spacing.sm,
  },
  fotoPlaceholderIcon: { fontSize: 40 },
  nome: { fontFamily: typography.fontFamily.titleBold, fontSize: typography.fontSize.xl, color: colors.text },
  raca: { fontFamily: typography.fontFamily.body, fontSize: typography.fontSize.md, color: colors.secondary, marginTop: 2, marginBottom: spacing.md },
  grid: { width: '100%', gap: spacing.xs },
  infoRow: { flexDirection: 'row', justifyContent: 'space-between', paddingVertical: spacing.xs, borderBottomWidth: 1, borderBottomColor: colors.border },
  infoLabel: { fontFamily: typography.fontFamily.bodyBold, fontSize: typography.fontSize.sm, color: colors.secondary },
  infoValue: { fontFamily: typography.fontFamily.body, fontSize: typography.fontSize.sm, color: colors.text },
  qrCard: {
    backgroundColor: colors.bg, borderRadius: 16, padding: spacing.lg,
    alignItems: 'center', marginBottom: spacing.md,
    elevation: 2, shadowColor: colors.black, shadowOffset: { width: 0, height: 2 }, shadowOpacity: 0.08, shadowRadius: 6,
  },
  qrTitulo: { fontFamily: typography.fontFamily.titleBold, fontSize: typography.fontSize.md, color: colors.text, marginBottom: spacing.md },
  qr: { width: 200, height: 200 },
  qrSub: { fontFamily: typography.fontFamily.body, fontSize: typography.fontSize.sm, color: colors.secondary, marginTop: spacing.sm, textAlign: 'center' },
  btnPdf: {
    backgroundColor: colors.primary, borderRadius: 10, paddingVertical: spacing.md, alignItems: 'center',
  },
  btnPdfLabel: { fontFamily: typography.fontFamily.bodyBold, fontSize: typography.fontSize.md, color: colors.white },
  centered: { flex: 1, alignItems: 'center', justifyContent: 'center', padding: spacing.lg },
  erroTexto: { fontFamily: typography.fontFamily.body, fontSize: typography.fontSize.md, color: colors.error, textAlign: 'center', marginBottom: spacing.md },
  btnRetry: { backgroundColor: colors.primary, borderRadius: 8, paddingVertical: spacing.sm, paddingHorizontal: spacing.lg },
  btnRetryLabel: { fontFamily: typography.fontFamily.bodyBold, fontSize: typography.fontSize.md, color: colors.white },
});
