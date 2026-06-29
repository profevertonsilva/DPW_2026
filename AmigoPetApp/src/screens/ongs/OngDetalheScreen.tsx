import React, { useEffect, useState } from 'react';
import {
  View, Text, ScrollView, StyleSheet, TouchableOpacity, ActivityIndicator, Linking,
} from 'react-native';
import { NativeStackScreenProps } from '@react-navigation/native-stack';
import { OngsStackParamList } from '../../navigation/stacks/OngsStack';
import { Avatar } from '../../components/ui/Avatar';
import { Section } from '../../components/ui/Section';
import { AnimalCard } from '../../components/domain/AnimalCard';
import { ongService } from '../../api/services/ongService';
import { Ong } from '../../types/Ong';
import { Animal } from '../../types/Animal';
import { colors } from '../../theme/colors';
import { typography } from '../../theme/typography';
import { spacing } from '../../theme/spacing';

type Props = NativeStackScreenProps<OngsStackParamList, 'OngDetalhe'>;

export function OngDetalheScreen({ route }: Props) {
  const { id } = route.params;
  const [ong, setOng] = useState<Ong | null>(null);
  const [animais, setAnimais] = useState<Animal[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    (async () => {
      try {
        const [o, a] = await Promise.all([ongService.buscarPorId(id), ongService.animais(id)]);
        setOng(o);
        setAnimais(a);
      } finally {
        setLoading(false);
      }
    })();
  }, [id]);

  if (loading) {
    return <View style={styles.center}><ActivityIndicator size="large" color={colors.primary} /></View>;
  }

  if (!ong) {
    return <View style={styles.center}><Text style={styles.erroText}>ONG não encontrada.</Text></View>;
  }

  const endereco = [ong.logradouro, ong.numero, ong.bairro, ong.cidade, ong.estado]
    .filter(Boolean).join(', ');

  return (
    <ScrollView style={styles.root} contentContainerStyle={styles.scroll}>
      <View style={styles.hero}>
        <Avatar uri={ong.foto ?? null} nome={ong.nome} size={80} />
        <Text style={styles.nome}>{ong.nome}</Text>
        <Text style={styles.cnpj}>CNPJ: {ong.cnpj}</Text>
      </View>

      <Section titulo="Contato">
        {ong.telefone_1 ? (
          <TelRow label="Telefone 1" numero={ong.telefone_1} />
        ) : null}
        {ong.telefone_2 ? (
          <TelRow label="Telefone 2" numero={ong.telefone_2} />
        ) : null}
        {ong.email ? (
          <InfoRow label="E-mail" value={ong.email} />
        ) : null}
      </Section>

      <Section titulo="Endereço">
        <Text style={styles.enderecoText}>{endereco || '—'}</Text>
        {ong.complemento ? <Text style={styles.complemento}>{ong.complemento}</Text> : null}
        <Text style={styles.cep}>CEP: {ong.cep}</Text>
      </Section>

      {animais.length > 0 && (
        <Section titulo={`Animais disponíveis (${animais.length})`}>
          {animais.map(a => (
            <AnimalCard key={a.id} animal={a} onPress={() => {}} />
          ))}
        </Section>
      )}
    </ScrollView>
  );
}

function TelRow({ label, numero }: { label: string; numero: string }) {
  return (
    <TouchableOpacity
      style={styles.infoRow}
      onPress={() => Linking.openURL(`tel:${numero.replace(/\D/g, '')}`)}
      activeOpacity={0.7}
    >
      <Text style={styles.infoLabel}>{label}</Text>
      <Text style={[styles.infoValue, styles.link]}>{numero}</Text>
    </TouchableOpacity>
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
  scroll: { padding: spacing.md },
  center: { flex: 1, justifyContent: 'center', alignItems: 'center' },
  erroText: { fontFamily: typography.fontFamily.body, color: colors.secondary, fontSize: typography.fontSize.md },
  hero: { alignItems: 'center', backgroundColor: colors.bg, borderRadius: 12, padding: spacing.lg, marginBottom: spacing.md, elevation: 2, shadowColor: colors.black, shadowOffset: { width: 0, height: 2 }, shadowOpacity: 0.06, shadowRadius: 6 },
  nome: { fontFamily: typography.fontFamily.titleBold, fontSize: typography.fontSize.xl, color: colors.text, marginTop: spacing.sm, textAlign: 'center' },
  cnpj: { fontFamily: typography.fontFamily.body, fontSize: typography.fontSize.xs, color: colors.secondary, marginTop: 4 },
  infoRow: { flexDirection: 'row', justifyContent: 'space-between', paddingVertical: spacing.xs, borderBottomWidth: 1, borderBottomColor: colors.border },
  infoLabel: { fontFamily: typography.fontFamily.bodyBold, fontSize: typography.fontSize.sm, color: colors.secondary },
  infoValue: { fontFamily: typography.fontFamily.body, fontSize: typography.fontSize.sm, color: colors.text },
  link: { color: colors.primary, textDecorationLine: 'underline' },
  enderecoText: { fontFamily: typography.fontFamily.body, fontSize: typography.fontSize.sm, color: colors.text },
  complemento: { fontFamily: typography.fontFamily.body, fontSize: typography.fontSize.sm, color: colors.secondary, marginTop: 2 },
  cep: { fontFamily: typography.fontFamily.body, fontSize: typography.fontSize.xs, color: colors.secondary, marginTop: 4 },
});
