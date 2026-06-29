import React, { useState } from 'react';
import {
  View, Text, ScrollView, StyleSheet, TouchableOpacity, ActivityIndicator,
} from 'react-native';
import { TextInput, Snackbar } from 'react-native-paper';
import MaskInput from 'react-native-mask-input';
import { useForm, Controller } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import { z } from 'zod';
import { useNavigation, useRoute } from '@react-navigation/native';
import type { RouteProp } from '@react-navigation/native';
import { vacinaService } from '../../api/services/vacinaService';
import { useAuth } from '../../hooks/useAuth';
import { colors } from '../../theme/colors';
import { typography } from '../../theme/typography';
import { spacing } from '../../theme/spacing';

const DATE_MASK = [/\d/, /\d/, '/', /\d/, /\d/, '/', /\d/, /\d/, /\d/, /\d/];

const schema = z.object({
  nome: z.string().min(2, 'Nome da vacina obrigatório'),
  data_aplicacao: z.string().min(10, 'Data inválida'),
  data_reforco: z.string().optional(),
  veterinario_nome: z.string().optional(),
  clinica_nome: z.string().optional(),
});

type FormData = z.infer<typeof schema>;

function parseDateToISO(br: string): string {
  const [dia, mes, ano] = br.split('/');
  return `${ano}-${mes}-${dia}`;
}

function formatDateToBR(iso: string): string {
  const [ano, mes, dia] = iso.split('-');
  return `${dia}/${mes}/${ano}`;
}

function hoje() {
  return formatDateToBR(new Date().toISOString().split('T')[0]);
}

export function AdicionarVacinaScreen() {
  const { user } = useAuth();
  const navigation = useNavigation();
  const route = useRoute<RouteProp<{ p: { animalId: number } }, 'p'>>();
  const animalId = (route.params as any)?.animalId as number;

  const [enviando, setEnviando] = useState(false);
  const [snack, setSnack] = useState({ visible: false, msg: '' });

  const { control, handleSubmit, formState: { errors } } = useForm<FormData>({
    resolver: zodResolver(schema),
    defaultValues: {
      nome: '', data_aplicacao: hoje(), data_reforco: '',
      veterinario_nome: '', clinica_nome: '',
    },
  });

  async function onSubmit(data: FormData) {
    setEnviando(true);
    try {
      await vacinaService.adicionar(
        {
          nome: data.nome,
          data_aplicacao: parseDateToISO(data.data_aplicacao),
          data_reforco: data.data_reforco ? parseDateToISO(data.data_reforco) : null,
          veterinario_nome: data.veterinario_nome || undefined,
          clinica_nome: data.clinica_nome || undefined,
          fk_animal_id: animalId,
        },
        user?.nome ?? 'Desconhecido',
      );
      navigation.goBack();
    } catch (err: any) {
      setSnack({ visible: true, msg: err?.message ?? 'Erro ao registrar vacina.' });
    } finally { setEnviando(false); }
  }

  return (
    <View style={styles.root}>
      <ScrollView contentContainerStyle={styles.scroll} keyboardShouldPersistTaps="handled">
        <Secao titulo="Registro de Vacina">
        <Campo label="Nome da vacina *" error={errors.nome?.message}>
          <Controller control={control} name="nome"
            render={({ field: { onChange, onBlur, value } }) => (
              <TextInput mode="outlined" value={value} onChangeText={onChange} onBlur={onBlur}
                placeholder="Ex: V8, Antirrábica, Bordetella..."
                outlineColor={colors.border} activeOutlineColor={colors.primary}
                style={styles.input} contentStyle={styles.inputContent} />
            )} />
        </Campo>

        <Campo label="Data de aplicação * (DD/MM/AAAA)" error={errors.data_aplicacao?.message}>
          <Controller control={control} name="data_aplicacao"
            render={({ field: { onChange, value } }) => (
              <MaskInput value={value} onChangeText={onChange} mask={DATE_MASK}
                style={styles.maskInput} keyboardType="numeric" />
            )} />
        </Campo>

        <Campo label="Data de reforço (DD/MM/AAAA, opcional)" error={errors.data_reforco?.message}>
          <Controller control={control} name="data_reforco"
            render={({ field: { onChange, value } }) => (
              <MaskInput value={value ?? ''} onChangeText={onChange} mask={DATE_MASK}
                style={styles.maskInput} keyboardType="numeric" />
            )} />
        </Campo>

        <Campo label="Veterinário responsável (opcional)" error={errors.veterinario_nome?.message}>
          <Controller control={control} name="veterinario_nome"
            render={({ field: { onChange, onBlur, value } }) => (
              <TextInput mode="outlined" value={value ?? ''} onChangeText={onChange} onBlur={onBlur}
                outlineColor={colors.border} activeOutlineColor={colors.primary}
                style={styles.input} contentStyle={styles.inputContent} />
            )} />
        </Campo>

        <Campo label="Clínica (opcional)" error={errors.clinica_nome?.message}>
          <Controller control={control} name="clinica_nome"
            render={({ field: { onChange, onBlur, value } }) => (
              <TextInput mode="outlined" value={value ?? ''} onChangeText={onChange} onBlur={onBlur}
                outlineColor={colors.border} activeOutlineColor={colors.primary}
                style={styles.input} contentStyle={styles.inputContent} />
            )} />
        </Campo>
        </Secao>

        <View style={styles.aviso}>
          <Text style={styles.avisoTexto}>🔒 Registros são permanentes e não podem ser editados ou excluídos.</Text>
        </View>

        <TouchableOpacity
          style={[styles.btnSalvar, enviando && styles.btnDisabled]}
          onPress={handleSubmit(onSubmit)}
          disabled={enviando}
          activeOpacity={0.85}>
          {enviando
            ? <ActivityIndicator color={colors.white} />
            : <Text style={styles.btnLabel}>Registrar vacina</Text>}
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
  input: { backgroundColor: colors.bg, height: 48 },
  inputContent: { fontFamily: typography.fontFamily.body, fontSize: typography.fontSize.md },
  maskInput: {
    fontFamily: typography.fontFamily.body, fontSize: typography.fontSize.md, color: colors.text,
    borderWidth: 1, borderColor: colors.border, borderRadius: 4,
    paddingHorizontal: spacing.md, paddingVertical: spacing.sm, backgroundColor: colors.bg,
  },
  aviso: { backgroundColor: '#FFF8E7', borderRadius: 10, padding: spacing.sm, marginVertical: spacing.sm, borderLeftWidth: 3, borderLeftColor: colors.accent },
  avisoTexto: { fontFamily: typography.fontFamily.body, fontSize: typography.fontSize.sm, color: colors.text },
  btnSalvar: { backgroundColor: colors.primary, borderRadius: 10, paddingVertical: spacing.md, alignItems: 'center', marginTop: spacing.sm },
  btnDisabled: { backgroundColor: colors.border },
  btnLabel: { fontFamily: typography.fontFamily.bodyBold, fontSize: typography.fontSize.md, color: colors.white },
  secao: { backgroundColor: colors.bg, borderRadius: 12, padding: spacing.md, marginBottom: spacing.md },
  secaoTitulo: {
    fontFamily: typography.fontFamily.titleBold, fontSize: typography.fontSize.md,
    color: colors.text, marginBottom: spacing.sm,
    borderLeftWidth: 3, borderLeftColor: colors.primary, paddingLeft: spacing.sm,
  },
});
