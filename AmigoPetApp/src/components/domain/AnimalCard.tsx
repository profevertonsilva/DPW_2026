import React from 'react';
import { View, Text, StyleSheet, TouchableOpacity } from 'react-native';
import { Animal } from '../../types/Animal';
import { Avatar } from '../ui/Avatar';
import { colors } from '../../theme/colors';
import { typography } from '../../theme/typography';
import { spacing } from '../../theme/spacing';

interface Props {
  animal: Animal;
  onPress: () => void;
}

const PORTE_LABEL: Record<string, string> = { pequeno: 'Pequeno', medio: 'Médio', grande: 'Grande' };
const SEXO_LABEL: Record<string, string> = { m: 'Macho', f: 'Fêmea' };

export function AnimalCard({ animal, onPress }: Props) {
  return (
    <TouchableOpacity style={styles.card} onPress={onPress} activeOpacity={0.85}>
      <Avatar uri={animal.foto} nome={animal.nome} size={72} />
      <View style={styles.info}>
        <Text style={styles.nome}>{animal.nome}</Text>
        <Text style={styles.raca}>{animal.raca ?? animal.especie}</Text>
        <View style={styles.tags}>
          {animal.porte && <Tag label={PORTE_LABEL[animal.porte] ?? animal.porte} />}
          <Tag label={SEXO_LABEL[animal.sexo] ?? animal.sexo} />
          {animal.idade_anos != null && (
            <Tag label={animal.idade_anos === 0 ? 'Filhote' : `${animal.idade_anos} ano(s)`} />
          )}
        </View>
      </View>
    </TouchableOpacity>
  );
}

function Tag({ label }: { label: string }) {
  return (
    <View style={styles.tag}>
      <Text style={styles.tagLabel}>{label}</Text>
    </View>
  );
}

const styles = StyleSheet.create({
  card: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: colors.bg,
    borderRadius: 12,
    padding: spacing.md,
    marginBottom: spacing.sm,
    shadowColor: '#000',
    shadowOpacity: 0.06,
    shadowRadius: 6,
    shadowOffset: { width: 0, height: 2 },
    elevation: 3,
  },
  info: { flex: 1, marginLeft: spacing.md },
  nome: {
    fontFamily: typography.fontFamily.titleBold,
    fontSize: typography.fontSize.md,
    color: colors.text,
  },
  raca: {
    fontFamily: typography.fontFamily.body,
    fontSize: typography.fontSize.sm,
    color: colors.secondary,
    marginTop: 2,
  },
  tags: { flexDirection: 'row', flexWrap: 'wrap', marginTop: spacing.xs },
  tag: {
    backgroundColor: colors.bgMuted,
    borderRadius: 8,
    paddingHorizontal: spacing.sm,
    paddingVertical: 3,
    marginRight: spacing.xs,
    marginTop: spacing.xs,
  },
  tagLabel: {
    fontFamily: typography.fontFamily.bodyBold,
    fontSize: typography.fontSize.xs,
    color: colors.secondary,
  },
});
