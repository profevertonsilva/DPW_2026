import React, { useState } from 'react';
import {
  View, Text, ScrollView, StyleSheet, TouchableOpacity, ActivityIndicator, Alert,
} from 'react-native';
import { TextInput, Snackbar } from 'react-native-paper';
import { useForm, Controller } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import { z } from 'zod';
import { useNavigation, useRoute } from '@react-navigation/native';
import type { NativeStackNavigationProp } from '@react-navigation/native-stack';
import type { RouteProp } from '@react-navigation/native';
import { avaliacaoService } from '../../api/services/avaliacaoService';
import { solicitacaoAdocaoService } from '../../api/services/solicitacaoAdocaoService';
import { useAuth } from '../../hooks/useAuth';
import { colors } from '../../theme/colors';
import { typography } from '../../theme/typography';
import { spacing } from '../../theme/spacing';
import type { TipoMoradia, ResultadoAvaliacao } from '../../types/Adocao';
import type { AdocoesOngStackParamList } from '../../navigation/stacks/AdocoesOngStack';

type Nav = NativeStackNavigationProp<AdocoesOngStackParamList, 'AvaliacaoAdotante'>;
type Route = RouteProp<AdocoesOngStackParamList, 'AvaliacaoAdotante'>;

const TIPOS_MORADIA: { label: string; value: TipoMoradia }[] = [
  { label: 'Casa c/ quintal', value: 'casa_com_quintal' },
  { label: 'Casa s/ quintal', value: 'casa_sem_quintal' },
  { label: 'Apartamento', value: 'apartamento' },
  { label: 'Outro', value: 'outro' },
];

const RESULTADOS: { label: string; value: ResultadoAvaliacao; cor: string }[] = [
  { label: 'Aprovado', value: 'aprovado', cor: colors.success },
  { label: 'Reprovado', value: 'reprovado', cor: colors.error },
  { label: 'Pendente de Informações', value: 'pendente_informacoes', cor: colors.accent },
];

const schema = z.object({
  tipo_moradia: z.enum(['casa_com_quintal', 'casa_sem_quintal', 'apartamento', 'outro'], { error: 'Selecione o tipo de moradia' }),
  experiencia_previa: z.boolean(),
  tem_criancas: z.boolean(),
  tem_outros_animais: z.boolean(),
  parecer: z.string().min(10, 'Parecer deve ter ao menos 10 caracteres'),
  resultado: z.enum(['aprovado', 'reprovado', 'pendente_informacoes'], { error: 'Selecione o resultado' }),
});

type FormData = z.infer<typeof schema>;

export function AvaliacaoAdotanteScreen() {
  const { user } = useAuth();
  const navigation = useNavigation<Nav>();
  const route = useRoute<Route>();
  const { solicitacaoId, adotanteNome } = route.params;

  const [enviando, setEnviando] = useState(false);
  const [snack, setSnack] = useState({ visible: false, msg: '' });

  const { control, handleSubmit, formState: { errors } } = useForm<FormData>({
    resolver: zodResolver(schema),
    defaultValues: {
      tipo_moradia: undefined as any,
      experiencia_previa: false,
      tem_criancas: false,
      tem_outros_animais: false,
      parecer: '',
      resultado: undefined as any,
    },
  });

  async function onSubmit(data: FormData) {
    setEnviando(true);
    try {
      await avaliacaoService.registrar(
        { fk_solicitacao_id: solicitacaoId, ...data },
        user?.nome ?? 'ONG',
      );
      if (data.resultado === 'aprovado') {
        await solicitacaoAdocaoService.avancarStatus(solicitacaoId, { status: 'Aprovado' });
      } else if (data.resultado === 'reprovado') {
        await solicitacaoAdocaoService.avancarStatus(solicitacaoId, { status: 'Recusado' });
      }
      Alert.alert('Avaliação registrada!', 'O adotante será notificado do resultado.', [
        { text: 'OK', onPress: () => navigation.goBack() },
      ]);
    } catch (err: any) {
      setSnack({ visible: true, msg: err?.message ?? 'Erro ao registrar avaliação.' });
    } finally { setEnviando(false); }
  }

  return (
    <View style={styles.root}>
      <ScrollView contentContainerStyle={styles.scroll} keyboardShouldPersistTaps="handled">
        <Text style={styles.heading}>Avaliação de Adotante</Text>
        <Text style={styles.sub}>Adotante: {adotanteNome}</Text>

        <Secao titulo="Perfil do Lar">
          <Campo label="Tipo de moradia *" error={errors.tipo_moradia?.message}>
            <Controller control={control} name="tipo_moradia"
              render={({ field: { onChange, value } }) => (
                <View style={styles.chips}>
                  {TIPOS_MORADIA.map(t => (
                    <TouchableOpacity key={t.value}
                      style={[styles.chip, value === t.value && styles.chipAtivo]}
                      onPress={() => onChange(t.value)}>
                      <Text style={[styles.chipLabel, value === t.value && styles.chipLabelAtivo]}>{t.label}</Text>
                    </TouchableOpacity>
                  ))}
                </View>
              )} />
          </Campo>

          <Campo label="Critérios do lar">
            <Controller control={control} name="experiencia_previa"
              render={({ field: { onChange, value } }) => (
                <BoolChip label="Tem experiência prévia com animais" value={value} onChange={onChange} />
              )} />
            <Controller control={control} name="tem_criancas"
              render={({ field: { onChange, value } }) => (
                <BoolChip label="Há crianças no lar" value={value} onChange={onChange} />
              )} />
            <Controller control={control} name="tem_outros_animais"
              render={({ field: { onChange, value } }) => (
                <BoolChip label="Há outros animais no lar" value={value} onChange={onChange} />
              )} />
          </Campo>
        </Secao>

        <Secao titulo="Parecer e Resultado">
          <Campo label="Parecer *" error={errors.parecer?.message}>
            <Controller control={control} name="parecer"
              render={({ field: { onChange, onBlur, value } }) => (
                <TextInput mode="outlined" value={value} onChangeText={onChange} onBlur={onBlur}
                  multiline numberOfLines={4} placeholder="Descreva a análise do perfil do adotante..."
                  outlineColor={colors.border} activeOutlineColor={colors.primary}
                  style={styles.inputMultiline} contentStyle={styles.inputContent} />
              )} />
          </Campo>

          <Campo label="Resultado *" error={errors.resultado?.message}>
            <Controller control={control} name="resultado"
              render={({ field: { onChange, value } }) => (
                <View style={styles.chips}>
                  {RESULTADOS.map(r => (
                    <TouchableOpacity key={r.value}
                      style={[styles.chip, value === r.value && { ...styles.chipAtivo, backgroundColor: r.cor, borderColor: r.cor }]}
                      onPress={() => onChange(r.value)}>
                      <Text style={[styles.chipLabel, value === r.value && styles.chipLabelAtivo]}>{r.label}</Text>
                    </TouchableOpacity>
                  ))}
                </View>
              )} />
          </Campo>
        </Secao>

        <View style={styles.aviso}>
          <Text style={styles.avisoTexto}>
            🔒 Esta avaliação é registrada no histórico e não pode ser alterada. Se o resultado for "Aprovado", a solicitação avança para Aprovado automaticamente.
          </Text>
        </View>

        <TouchableOpacity
          style={[styles.btnSalvar, enviando && styles.btnDisabled]}
          onPress={handleSubmit(onSubmit)}
          disabled={enviando}
          activeOpacity={0.85}>
          {enviando
            ? <ActivityIndicator color={colors.white} />
            : <Text style={styles.btnLabel}>Registrar avaliação</Text>}
        </TouchableOpacity>
      </ScrollView>

      <Snackbar visible={snack.visible} onDismiss={() => setSnack(s => ({ ...s, visible: false }))}
        duration={4000} style={{ backgroundColor: colors.error }}>
        {snack.msg}
      </Snackbar>
    </View>
  );
}

function BoolChip({ label, value, onChange }: { label: string; value: boolean; onChange: (v: boolean) => void }) {
  return (
    <TouchableOpacity style={styles.boolRow} onPress={() => onChange(!value)} activeOpacity={0.8}>
      <View style={[styles.boolBox, value && styles.boolBoxAtivo]}>
        {value && <Text style={styles.boolCheck}>✓</Text>}
      </View>
      <Text style={styles.boolLabel}>{label}</Text>
    </TouchableOpacity>
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
      {label ? <Text style={styles.campoLabel}>{label}</Text> : null}
      {children}
      {error && <Text style={styles.campoErro}>{error}</Text>}
    </View>
  );
}

const styles = StyleSheet.create({
  root: { flex: 1, backgroundColor: colors.bgMuted },
  scroll: { padding: spacing.md, paddingBottom: spacing.xxl },
  heading: { fontFamily: typography.fontFamily.titleBold, fontSize: typography.fontSize.xl, color: colors.text, marginBottom: 2 },
  sub: { fontFamily: typography.fontFamily.body, fontSize: typography.fontSize.md, color: colors.secondary, marginBottom: spacing.lg },
  campo: { marginBottom: spacing.sm },
  campoLabel: { fontFamily: typography.fontFamily.body, fontSize: typography.fontSize.sm, color: colors.secondary, marginBottom: 4 },
  campoErro: { fontFamily: typography.fontFamily.body, fontSize: typography.fontSize.xs, color: colors.error, marginTop: 2 },
  inputMultiline: { backgroundColor: colors.bg },
  inputContent: { fontFamily: typography.fontFamily.body, fontSize: typography.fontSize.md },
  chips: { flexDirection: 'row', flexWrap: 'wrap', gap: spacing.xs },
  chip: { borderWidth: 1, borderColor: colors.border, borderRadius: 20, paddingVertical: 6, paddingHorizontal: spacing.md },
  chipAtivo: { backgroundColor: colors.primary, borderColor: colors.primary },
  chipLabel: { fontFamily: typography.fontFamily.body, fontSize: typography.fontSize.sm, color: colors.text },
  chipLabelAtivo: { color: colors.white },
  boolRow: { flexDirection: 'row', alignItems: 'center', paddingVertical: spacing.xs },
  boolBox: { width: 20, height: 20, borderRadius: 4, borderWidth: 2, borderColor: colors.border, marginRight: spacing.sm, justifyContent: 'center', alignItems: 'center' },
  boolBoxAtivo: { backgroundColor: colors.primary, borderColor: colors.primary },
  boolCheck: { color: colors.white, fontSize: 12 },
  boolLabel: { fontFamily: typography.fontFamily.body, fontSize: typography.fontSize.sm, color: colors.text },
  secao: { backgroundColor: colors.bg, borderRadius: 12, padding: spacing.md, marginBottom: spacing.md },
  secaoTitulo: {
    fontFamily: typography.fontFamily.titleBold, fontSize: typography.fontSize.md,
    color: colors.text, marginBottom: spacing.sm,
    borderLeftWidth: 3, borderLeftColor: colors.primary, paddingLeft: spacing.sm,
  },
  aviso: { backgroundColor: '#FFF8E7', borderRadius: 10, padding: spacing.sm, marginVertical: spacing.sm, borderLeftWidth: 3, borderLeftColor: colors.accent },
  avisoTexto: { fontFamily: typography.fontFamily.body, fontSize: typography.fontSize.sm, color: colors.text, lineHeight: 20 },
  btnSalvar: { backgroundColor: colors.primary, borderRadius: 10, paddingVertical: spacing.md, alignItems: 'center', marginTop: spacing.sm },
  btnDisabled: { backgroundColor: colors.border },
  btnLabel: { fontFamily: typography.fontFamily.bodyBold, fontSize: typography.fontSize.md, color: colors.white },
});
