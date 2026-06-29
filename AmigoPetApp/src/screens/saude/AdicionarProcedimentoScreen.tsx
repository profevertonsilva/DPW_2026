import React, { useState } from 'react';
import {
  View, Text, ScrollView, StyleSheet, TouchableOpacity, ActivityIndicator, Alert,
} from 'react-native';
import { TextInput, Snackbar } from 'react-native-paper';
import MaskInput from 'react-native-mask-input';
import * as DocumentPicker from 'expo-document-picker';
import { useForm, Controller } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import { z } from 'zod';
import { useNavigation, useRoute } from '@react-navigation/native';
import type { RouteProp } from '@react-navigation/native';
import { procedimentoService } from '../../api/services/procedimentoService';
import { uploadService } from '../../api/services/uploadService';
import { useAuth } from '../../hooks/useAuth';
import { colors } from '../../theme/colors';
import { typography } from '../../theme/typography';
import { spacing } from '../../theme/spacing';
import type { TipoProcedimento } from '../../types/Saude';

const DATE_MASK = [/\d/, /\d/, '/', /\d/, /\d/, '/', /\d/, /\d/, /\d/, /\d/];

const TIPOS: { label: string; value: TipoProcedimento }[] = [
  { label: 'Consulta', value: 'consulta' },
  { label: 'Cirurgia', value: 'cirurgia' },
  { label: 'Exame', value: 'exame' },
  { label: 'Castração', value: 'castracao' },
  { label: 'Outro', value: 'outro' },
];

const schema = z.object({
  nome: z.string().min(2, 'Nome do procedimento obrigatório'),
  tipo: z.enum(['consulta', 'cirurgia', 'exame', 'castracao', 'outro'], { error: 'Selecione o tipo' }),
  data: z.string().min(10, 'Data inválida'),
  veterinario_nome: z.string().optional(),
  observacoes: z.string().optional(),
});

type FormData = z.infer<typeof schema>;

function parseDateToISO(br: string): string {
  const [dia, mes, ano] = br.split('/');
  return `${ano}-${mes}-${dia}`;
}

function hoje() {
  const d = new Date();
  const pad = (n: number) => String(n).padStart(2, '0');
  return `${pad(d.getDate())}/${pad(d.getMonth() + 1)}/${d.getFullYear()}`;
}

export function AdicionarProcedimentoScreen() {
  const { user } = useAuth();
  const navigation = useNavigation();
  const route = useRoute<RouteProp<{ p: { animalId: number } }, 'p'>>();
  const animalId = (route.params as any)?.animalId as number;

  const [enviando, setEnviando] = useState(false);
  const [uploadandoAnexo, setUploadandoAnexo] = useState(false);
  const [anexoUrl, setAnexoUrl] = useState<string | null>(null);
  const [anexoNome, setAnexoNome] = useState<string | null>(null);
  const [snack, setSnack] = useState({ visible: false, msg: '' });

  const { control, handleSubmit, formState: { errors } } = useForm<FormData>({
    resolver: zodResolver(schema),
    defaultValues: { nome: '', tipo: undefined as any, data: hoje(), veterinario_nome: '', observacoes: '' },
  });

  async function escolherAnexo() {
    try {
      const result = await DocumentPicker.getDocumentAsync({
        type: ['application/pdf', 'image/*'],
        copyToCacheDirectory: true,
      });
      if (result.canceled) return;
      const file = result.assets[0];
      if (!file) return;
      const tamanhoMb = (file.size ?? 0) / (1024 * 1024);
      if (tamanhoMb > 10) { Alert.alert('Arquivo muito grande', 'Máximo 10 MB.'); return; }
      setUploadandoAnexo(true);
      const url = await uploadService.enviarArquivo(file.uri, file.name);
      setAnexoUrl(url);
      setAnexoNome(file.name);
    } catch { /* cancelado */ } finally { setUploadandoAnexo(false); }
  }

  async function onSubmit(data: FormData) {
    setEnviando(true);
    try {
      await procedimentoService.adicionar(
        {
          nome: data.nome,
          tipo: data.tipo,
          data: parseDateToISO(data.data),
          veterinario_nome: data.veterinario_nome || undefined,
          observacoes: data.observacoes || undefined,
          anexo_url: anexoUrl ?? undefined,
          fk_animal_id: animalId,
        },
        user?.nome ?? 'Desconhecido',
      );
      navigation.goBack();
    } catch (err: any) {
      setSnack({ visible: true, msg: err?.message ?? 'Erro ao registrar procedimento.' });
    } finally { setEnviando(false); }
  }

  return (
    <View style={styles.root}>
      <ScrollView contentContainerStyle={styles.scroll} keyboardShouldPersistTaps="handled">
        <Secao titulo="Registro de Procedimento">
        <Campo label="Nome do procedimento *" error={errors.nome?.message}>
          <Controller control={control} name="nome"
            render={({ field: { onChange, onBlur, value } }) => (
              <TextInput mode="outlined" value={value} onChangeText={onChange} onBlur={onBlur}
                placeholder="Ex: Exame de sangue, Cirurgia ortopédica..."
                outlineColor={colors.border} activeOutlineColor={colors.primary}
                style={styles.input} contentStyle={styles.inputContent} />
            )} />
        </Campo>

        <Campo label="Tipo *" error={errors.tipo?.message}>
          <Controller control={control} name="tipo"
            render={({ field: { onChange, value } }) => (
              <View style={styles.chips}>
                {TIPOS.map(t => (
                  <TouchableOpacity key={t.value}
                    style={[styles.chip, value === t.value && styles.chipAtivo]}
                    onPress={() => onChange(t.value)}>
                    <Text style={[styles.chipLabel, value === t.value && styles.chipLabelAtivo]}>{t.label}</Text>
                  </TouchableOpacity>
                ))}
              </View>
            )} />
        </Campo>

        <Campo label="Data * (DD/MM/AAAA)" error={errors.data?.message}>
          <Controller control={control} name="data"
            render={({ field: { onChange, value } }) => (
              <MaskInput value={value} onChangeText={onChange} mask={DATE_MASK}
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

        <Campo label="Observações (opcional)" error={errors.observacoes?.message}>
          <Controller control={control} name="observacoes"
            render={({ field: { onChange, onBlur, value } }) => (
              <TextInput mode="outlined" value={value ?? ''} onChangeText={onChange} onBlur={onBlur}
                multiline numberOfLines={3} outlineColor={colors.border} activeOutlineColor={colors.primary}
                style={styles.inputMultiline} contentStyle={styles.inputContent} />
            )} />
        </Campo>

        <Campo label="Anexo — laudo/receita PDF ou imagem (opcional, max 10 MB)">
          <TouchableOpacity
            style={[styles.btnAnexo, uploadandoAnexo && styles.btnDisabled]}
            onPress={escolherAnexo}
            disabled={uploadandoAnexo}
            activeOpacity={0.8}>
            {uploadandoAnexo
              ? <ActivityIndicator color={colors.primary} size="small" />
              : <Text style={styles.btnAnexoLabel}>{anexoNome ?? '📎 Selecionar arquivo'}</Text>}
          </TouchableOpacity>
          {anexoUrl && <Text style={styles.anexoOk}>✓ Arquivo enviado</Text>}
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
            : <Text style={styles.btnLabel}>Registrar procedimento</Text>}
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
  inputMultiline: { backgroundColor: colors.bg },
  inputContent: { fontFamily: typography.fontFamily.body, fontSize: typography.fontSize.md },
  maskInput: {
    fontFamily: typography.fontFamily.body, fontSize: typography.fontSize.md, color: colors.text,
    borderWidth: 1, borderColor: colors.border, borderRadius: 4,
    paddingHorizontal: spacing.md, paddingVertical: spacing.sm, backgroundColor: colors.bg,
  },
  chips: { flexDirection: 'row', flexWrap: 'wrap', gap: spacing.xs },
  chip: { borderWidth: 1, borderColor: colors.border, borderRadius: 20, paddingVertical: 6, paddingHorizontal: spacing.md },
  chipAtivo: { backgroundColor: colors.primary, borderColor: colors.primary },
  chipLabel: { fontFamily: typography.fontFamily.body, fontSize: typography.fontSize.sm, color: colors.text },
  chipLabelAtivo: { color: colors.white },
  btnAnexo: {
    borderWidth: 1, borderColor: colors.primary, borderRadius: 8, borderStyle: 'dashed',
    padding: spacing.sm, alignItems: 'center',
  },
  btnAnexoLabel: { fontFamily: typography.fontFamily.body, fontSize: typography.fontSize.sm, color: colors.primary },
  anexoOk: { fontFamily: typography.fontFamily.body, fontSize: typography.fontSize.xs, color: colors.success, marginTop: 4 },
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
