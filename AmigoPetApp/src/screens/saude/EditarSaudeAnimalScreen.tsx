import React, { useCallback, useEffect, useState } from 'react';
import {
  View, Text, ScrollView, StyleSheet, TouchableOpacity, ActivityIndicator, Alert,
} from 'react-native';
import { TextInput, Snackbar } from 'react-native-paper';
import { useForm, Controller } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import { z } from 'zod';
import { useNavigation, useRoute } from '@react-navigation/native';
import type { RouteProp } from '@react-navigation/native';
import type { NativeStackNavigationProp } from '@react-navigation/native-stack';
import { saudeService } from '../../api/services/saudeService';
import { usePermissions } from '../../permissions/usePermissions';
import { EmptyState } from '../../components/ui/EmptyState';
import { colors } from '../../theme/colors';
import { typography } from '../../theme/typography';
import { spacing } from '../../theme/spacing';
import type { HomeStackParamList } from '../../navigation/stacks/HomeStack';

type Nav = NativeStackNavigationProp<HomeStackParamList, 'EditarSaudeAnimal'>;
type Route = RouteProp<HomeStackParamList, 'EditarSaudeAnimal'>;

const schema = z.object({
  apto_para_adocao: z.boolean(),
  condicao_geral: z.string().optional(),
  temperamento: z.string().optional(),
  necessidades_especiais: z.string().optional(),
});

type FormData = z.infer<typeof schema>;

export function EditarSaudeAnimalScreen() {
  const navigation = useNavigation<Nav>();
  const route = useRoute<Route>();
  const animalId = route.params.animalId;
  const { has } = usePermissions();

  const podeEditar = has('podeCadastrarAnimal') || has('podeRegistrarProcedimento');

  const [carregando, setCarregando] = useState(true);
  const [salvando, setSalvando] = useState(false);
  const [snack, setSnack] = useState({ visible: false, msg: '' });

  const { control, handleSubmit, reset, formState: { errors } } = useForm<FormData>({
    resolver: zodResolver(schema),
    defaultValues: { apto_para_adocao: true, condicao_geral: '', temperamento: '', necessidades_especiais: '' },
  });

  const carregar = useCallback(async () => {
    setCarregando(true);
    try {
      const saude = await saudeService.obter(animalId);
      reset({
        apto_para_adocao: saude.apto_para_adocao,
        condicao_geral: saude.condicao_geral ?? '',
        temperamento: saude.temperamento ?? '',
        necessidades_especiais: saude.necessidades_especiais ?? '',
      });
    } finally { setCarregando(false); }
  }, [animalId, reset]);

  useEffect(() => { carregar(); }, [carregar]);

  if (!podeEditar) {
    return (
      <View style={styles.centered}>
        <EmptyState
          icon="🔒"
          title="Sem permissão"
          message="Apenas ONGs e veterinários podem editar a condição de saúde."
        />
      </View>
    );
  }

  if (carregando) return <View style={styles.centered}><ActivityIndicator size="large" color={colors.primary} /></View>;

  async function onSubmit(data: FormData) {
    setSalvando(true);
    try {
      await saudeService.atualizar(animalId, {
        apto_para_adocao: data.apto_para_adocao,
        condicao_geral: data.condicao_geral || null,
        temperamento: data.temperamento || null,
        necessidades_especiais: data.necessidades_especiais || null,
      });
      navigation.goBack();
    } catch (err: any) {
      setSnack({ visible: true, msg: err?.message ?? 'Erro ao salvar.' });
    } finally { setSalvando(false); }
  }

  return (
    <View style={styles.root}>
      <ScrollView contentContainerStyle={styles.scroll} keyboardShouldPersistTaps="handled">
        <Secao titulo="Condição de Saúde">
        <Campo label="Apto para adoção?">
          <Controller control={control} name="apto_para_adocao"
            render={({ field: { onChange, value } }) => (
              <View style={styles.chips}>
                <TouchableOpacity style={[styles.chip, value && styles.chipAtivo]} onPress={() => onChange(true)}>
                  <Text style={[styles.chipLabel, value && styles.chipLabelAtivo]}>Sim</Text>
                </TouchableOpacity>
                <TouchableOpacity style={[styles.chip, !value && { ...styles.chipAtivo, backgroundColor: colors.error, borderColor: colors.error }]} onPress={() => onChange(false)}>
                  <Text style={[styles.chipLabel, !value && styles.chipLabelAtivo]}>Não</Text>
                </TouchableOpacity>
              </View>
            )} />
        </Campo>

        <Campo label="Condição de saúde geral (opcional)">
          <Controller control={control} name="condicao_geral"
            render={({ field: { onChange, onBlur, value } }) => (
              <TextInput mode="outlined" value={value ?? ''} onChangeText={onChange} onBlur={onBlur}
                multiline numberOfLines={3} placeholder="Ex: Excelente. Vacinado e castrado."
                outlineColor={colors.border} activeOutlineColor={colors.primary}
                style={styles.inputMultiline} contentStyle={styles.inputContent} />
            )} />
        </Campo>

        <Campo label="Temperamento (opcional)">
          <Controller control={control} name="temperamento"
            render={({ field: { onChange, onBlur, value } }) => (
              <TextInput mode="outlined" value={value ?? ''} onChangeText={onChange} onBlur={onBlur}
                multiline numberOfLines={3} placeholder="Ex: Dócil, brincalhão, sociável com crianças..."
                outlineColor={colors.border} activeOutlineColor={colors.primary}
                style={styles.inputMultiline} contentStyle={styles.inputContent} />
            )} />
        </Campo>

        <Campo label="Necessidades especiais (opcional)">
          <Controller control={control} name="necessidades_especiais"
            render={({ field: { onChange, onBlur, value } }) => (
              <TextInput mode="outlined" value={value ?? ''} onChangeText={onChange} onBlur={onBlur}
                multiline numberOfLines={3} placeholder="Ex: Intolerância a ração com grãos, medicação diária..."
                outlineColor={colors.border} activeOutlineColor={colors.primary}
                style={styles.inputMultiline} contentStyle={styles.inputContent} />
            )} />
        </Campo>
        </Secao>

        <TouchableOpacity
          style={[styles.btnSalvar, salvando && styles.btnDisabled]}
          onPress={handleSubmit(onSubmit)}
          disabled={salvando}
          activeOpacity={0.85}>
          {salvando
            ? <ActivityIndicator color={colors.white} />
            : <Text style={styles.btnLabel}>Salvar</Text>}
        </TouchableOpacity>
      </ScrollView>

      <Snackbar visible={snack.visible} onDismiss={() => setSnack(s => ({ ...s, visible: false }))}
        duration={4000} style={{ backgroundColor: colors.error }}>
        {snack.msg}
      </Snackbar>
    </View>
  );
}

function Secao({ titulo, children }: { titulo: string; children: React.ReactNode }) {
  return (
    <View style={styles.secao}>
      <Text style={styles.secaoTitulo}>{titulo}</Text>
      {children}
    </View>
  );
}

function Campo({ label, children, error }: { label: string; children: React.ReactNode; error?: string }) {
  return (
    <View style={styles.campo}>
      <Text style={styles.campoLabel}>{label}</Text>
      {children}
      {error && <Text style={styles.campoErro}>{error}</Text>}
    </View>
  );
}

const styles = StyleSheet.create({
  root: { flex: 1, backgroundColor: colors.bgMuted },
  scroll: { padding: spacing.md, paddingBottom: spacing.xxl },
  campo: { marginBottom: spacing.sm },
  campoLabel: { fontFamily: typography.fontFamily.body, fontSize: typography.fontSize.sm, color: colors.secondary, marginBottom: 4 },
  campoErro: { fontFamily: typography.fontFamily.body, fontSize: typography.fontSize.xs, color: colors.error, marginTop: 2 },
  inputMultiline: { backgroundColor: colors.bg },
  inputContent: { fontFamily: typography.fontFamily.body, fontSize: typography.fontSize.md },
  chips: { flexDirection: 'row', gap: spacing.sm },
  chip: { borderWidth: 1, borderColor: colors.border, borderRadius: 20, paddingVertical: 6, paddingHorizontal: spacing.lg },
  chipAtivo: { backgroundColor: colors.success, borderColor: colors.success },
  chipLabel: { fontFamily: typography.fontFamily.body, fontSize: typography.fontSize.sm, color: colors.text },
  chipLabelAtivo: { color: colors.white },
  btnSalvar: { backgroundColor: colors.primary, borderRadius: 10, paddingVertical: spacing.md, alignItems: 'center', marginTop: spacing.sm },
  btnDisabled: { backgroundColor: colors.border },
  btnLabel: { fontFamily: typography.fontFamily.bodyBold, fontSize: typography.fontSize.md, color: colors.white },
  centered: { flex: 1, alignItems: 'center', justifyContent: 'center' },
  secao: { backgroundColor: colors.bg, borderRadius: 12, padding: spacing.md, marginBottom: spacing.md },
  secaoTitulo: {
    fontFamily: typography.fontFamily.titleBold, fontSize: typography.fontSize.md,
    color: colors.text, marginBottom: spacing.sm,
    borderLeftWidth: 3, borderLeftColor: colors.primary, paddingLeft: spacing.sm,
  },
});
