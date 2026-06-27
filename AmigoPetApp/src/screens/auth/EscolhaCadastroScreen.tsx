import React from 'react';
import { View, Text, StyleSheet, TouchableOpacity, ScrollView } from 'react-native';
import { useNavigation } from '@react-navigation/native';
import type { NativeStackNavigationProp } from '@react-navigation/native-stack';
import type { AuthStackParamList } from '../../navigation/AuthStack';
import { colors } from '../../theme/colors';
import { typography } from '../../theme/typography';
import { spacing } from '../../theme/spacing';

type Nav = NativeStackNavigationProp<AuthStackParamList>;

// Anel colorido por opção (verde / laranja / azul do theme), na ordem de OPCOES
const ICONE_CORES = [colors.primary, colors.accent, colors.info];

const OPCOES = [
  {
    rota: 'CadastroAdotante' as const,
    titulo: 'Pessoa / Adotante',
    descricao: 'Quero adotar um animal e acompanhar meu processo de adoção.',
    icone: '🏠',
  },
  {
    rota: 'CadastroOng' as const,
    titulo: 'ONG / Protetor',
    descricao: 'Sou uma organização ou protetor independente e quero publicar animais para adoção.',
    icone: '🏥',
  },
  {
    rota: 'CadastroVeterinario' as const,
    titulo: 'Veterinário',
    descricao: 'Sou médico veterinário e quero acompanhar a saúde dos animais na plataforma.',
    icone: '🩺',
  },
] as const;

export function EscolhaCadastroScreen() {
  const navigation = useNavigation<Nav>();

  return (
    <View style={styles.root}>
      <ScrollView contentContainerStyle={styles.scroll} keyboardShouldPersistTaps="handled">
        <TouchableOpacity style={styles.voltar} onPress={() => navigation.goBack()}>
          <Text style={styles.voltarTexto}>← Voltar</Text>
        </TouchableOpacity>

        <Text style={styles.heading}>Criar conta</Text>
        <Text style={styles.sub}>Qual é o seu perfil?</Text>

        {OPCOES.map((op, i) => (
          <TouchableOpacity
            key={op.rota}
            style={styles.card}
            onPress={() => navigation.navigate(op.rota)}
            activeOpacity={0.8}
          >
            <View style={[styles.iconeBadge, { borderColor: ICONE_CORES[i] }]}>
              <Text style={styles.icone}>{op.icone}</Text>
            </View>
            <View style={styles.cardTexto}>
              <Text style={styles.cardTitulo}>{op.titulo}</Text>
              <Text style={styles.cardDesc}>{op.descricao}</Text>
            </View>
            <Text style={styles.chevron}>›</Text>
          </TouchableOpacity>
        ))}
      </ScrollView>
    </View>
  );
}

const styles = StyleSheet.create({
  root: { flex: 1, backgroundColor: colors.bgMuted },
  scroll: { padding: spacing.md, paddingBottom: spacing.xxl },
  voltar: { marginBottom: spacing.md },
  voltarTexto: { fontFamily: typography.fontFamily.body, color: colors.primary, fontSize: typography.fontSize.md },
  heading: { fontFamily: typography.fontFamily.titleBold, fontSize: typography.fontSize.xxl, color: colors.text, marginBottom: 4 },
  sub: { fontFamily: typography.fontFamily.body, fontSize: typography.fontSize.md, color: colors.secondary, marginBottom: spacing.lg },
  card: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: colors.bg,
    borderRadius: 12,
    padding: spacing.md,
    marginBottom: spacing.sm,
    shadowColor: colors.black,
    shadowOffset: { width: 0, height: 1 },
    shadowOpacity: 0.06,
    shadowRadius: 4,
    elevation: 2,
  },
  iconeBadge: {
    width: 52,
    height: 52,
    borderRadius: 26,
    borderWidth: 2,
    backgroundColor: colors.bgMuted,
    alignItems: 'center',
    justifyContent: 'center',
    marginRight: spacing.md,
  },
  icone: { fontSize: 26 },
  cardTexto: { flex: 1 },
  cardTitulo: { fontFamily: typography.fontFamily.bodyBold, fontSize: typography.fontSize.md, color: colors.text, marginBottom: 2 },
  cardDesc: { fontFamily: typography.fontFamily.body, fontSize: typography.fontSize.sm, color: colors.secondary },
  chevron: { fontSize: 24, color: colors.secondary, marginLeft: spacing.sm },
});
