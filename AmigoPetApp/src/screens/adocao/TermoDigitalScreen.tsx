import React, { useCallback, useEffect, useState } from 'react';
import {
  View, Text, ScrollView, StyleSheet, TouchableOpacity, ActivityIndicator, Linking, Alert,
} from 'react-native';
import { useNavigation, useRoute } from '@react-navigation/native';
import type { NativeStackNavigationProp } from '@react-navigation/native-stack';
import type { RouteProp } from '@react-navigation/native';
import { termoService } from '../../api/services/termoService';
import { colors } from '../../theme/colors';
import { typography } from '../../theme/typography';
import { spacing } from '../../theme/spacing';
import type { Termo } from '../../types/Adocao';
import type { AdocaoStackParamList } from '../../navigation/stacks/AdocaoStack';

type Nav = NativeStackNavigationProp<AdocaoStackParamList, 'TermoDigital'>;
type Route = RouteProp<AdocaoStackParamList, 'TermoDigital'>;

export function TermoDigitalScreen() {
  const navigation = useNavigation<Nav>();
  const route = useRoute<Route>();
  const { solicitacaoId } = route.params;

  const [termo, setTermo] = useState<Termo | null>(null);
  const [carregando, setCarregando] = useState(true);
  const [aceite, setAceite] = useState(false);
  const [assinando, setAssinando] = useState(false);

  const carregar = useCallback(async () => {
    setCarregando(true);
    try {
      setTermo(await termoService.obter(solicitacaoId));
    } finally { setCarregando(false); }
  }, [solicitacaoId]);

  useEffect(() => { carregar(); }, [carregar]);

  async function assinar() {
    if (!aceite) return;
    Alert.alert(
      'Confirmar assinatura',
      'Ao confirmar, você assina eletronicamente o Termo de Responsabilidade. Esta ação não pode ser desfeita.',
      [
        { text: 'Cancelar', style: 'cancel' },
        {
          text: 'Assinar e Confirmar',
          onPress: async () => {
            setAssinando(true);
            try {
              await termoService.assinar(solicitacaoId, {
                aceite: true,
                timestamp: new Date().toISOString(),
              });
              // A API já move o status para "Concluído" ao assinar o termo
              Alert.alert('Adoção Concluída! 🎉', 'Parabéns! O termo foi assinado e a adoção foi concluída.', [
                { text: 'OK', onPress: () => navigation.popToTop() },
              ]);
            } catch (err: any) {
              Alert.alert('Erro', err?.message ?? 'Erro ao assinar o termo.');
            } finally { setAssinando(false); }
          },
        },
      ],
    );
  }

  if (carregando) return <View style={styles.centered}><ActivityIndicator size="large" color={colors.primary} /></View>;

  if (!termo) return (
    <View style={styles.centered}>
      <Text style={styles.erroTexto}>Não foi possível carregar o termo.</Text>
    </View>
  );

  return (
    <View style={styles.root}>
      <ScrollView contentContainerStyle={styles.scroll}>
        <View style={styles.aviso}>
          <Text style={styles.avisoTexto}>
            📜 Leia atentamente o termo abaixo. Ao assinar, você concorda com todas as cláusulas.
            A assinatura eletrônica registra data, hora e IP de origem conforme a Lei nº 14.063/2020.
          </Text>
        </View>

        <View style={styles.termoCard}>
          <Text style={styles.termoTexto}>{termo.conteudo_texto}</Text>
        </View>

        <TouchableOpacity style={styles.btnPdf}
          onPress={() => Linking.openURL(termo.pdf_url)}
          activeOpacity={0.8}>
          <Text style={styles.btnPdfLabel}>📄 Ver PDF do Termo</Text>
        </TouchableOpacity>

        <TouchableOpacity style={styles.checkRow} onPress={() => setAceite(v => !v)} activeOpacity={0.8}>
          <View style={[styles.checkbox, aceite && styles.checkboxAtivo]}>
            {aceite && <Text style={styles.checkmark}>✓</Text>}
          </View>
          <Text style={styles.checkLabel}>Li e compreendi o Termo de Responsabilidade e concordo com todas as suas cláusulas.</Text>
        </TouchableOpacity>
      </ScrollView>

      <View style={styles.footer}>
        <TouchableOpacity
          style={[styles.btnAssinar, (!aceite || assinando) && styles.btnDisabled]}
          onPress={assinar}
          disabled={!aceite || assinando}
          activeOpacity={0.85}>
          {assinando
            ? <ActivityIndicator color={colors.white} />
            : <Text style={styles.btnAssinarLabel}>✍️ Assinar e Concluir Adoção</Text>}
        </TouchableOpacity>
      </View>
    </View>
  );
}

const styles = StyleSheet.create({
  root: { flex: 1, backgroundColor: colors.bgMuted },
  scroll: { padding: spacing.md, paddingBottom: spacing.xxl },
  aviso: { backgroundColor: '#FFF8E7', borderRadius: 10, padding: spacing.md, marginBottom: spacing.md, borderLeftWidth: 3, borderLeftColor: colors.accent },
  avisoTexto: { fontFamily: typography.fontFamily.body, fontSize: typography.fontSize.sm, color: colors.text, lineHeight: 20 },
  termoCard: { backgroundColor: colors.bg, borderRadius: 12, padding: spacing.md, marginBottom: spacing.md, borderWidth: 1, borderColor: colors.border },
  termoTexto: { fontFamily: typography.fontFamily.body, fontSize: typography.fontSize.sm, color: colors.text, lineHeight: 22 },
  btnPdf: { alignItems: 'center', paddingVertical: spacing.sm, marginBottom: spacing.md },
  btnPdfLabel: { fontFamily: typography.fontFamily.bodyBold, fontSize: typography.fontSize.sm, color: colors.primary },
  checkRow: { flexDirection: 'row', alignItems: 'flex-start', backgroundColor: colors.bg, borderRadius: 10, padding: spacing.md },
  checkbox: { width: 22, height: 22, borderRadius: 4, borderWidth: 2, borderColor: colors.border, marginRight: spacing.sm, justifyContent: 'center', alignItems: 'center', marginTop: 2, flexShrink: 0 },
  checkboxAtivo: { backgroundColor: colors.primary, borderColor: colors.primary },
  checkmark: { color: colors.white, fontSize: 14, fontFamily: typography.fontFamily.bodyBold },
  checkLabel: { fontFamily: typography.fontFamily.body, fontSize: typography.fontSize.sm, color: colors.text, flex: 1, lineHeight: 20 },
  footer: { padding: spacing.md, backgroundColor: colors.bg, borderTopWidth: 1, borderTopColor: colors.border },
  btnAssinar: { backgroundColor: colors.success, borderRadius: 10, paddingVertical: spacing.md, alignItems: 'center' },
  btnDisabled: { backgroundColor: colors.border },
  btnAssinarLabel: { fontFamily: typography.fontFamily.titleBold, fontSize: typography.fontSize.md, color: colors.white },
  centered: { flex: 1, alignItems: 'center', justifyContent: 'center', padding: spacing.lg },
  erroTexto: { fontFamily: typography.fontFamily.body, fontSize: typography.fontSize.md, color: colors.error, textAlign: 'center' },
});
