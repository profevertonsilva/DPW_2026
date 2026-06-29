import React, { useCallback, useEffect, useState } from 'react';
import {
  View, Text, ScrollView, StyleSheet, TouchableOpacity, ActivityIndicator,
} from 'react-native';
import { TextInput, Snackbar } from 'react-native-paper';
import MaskInput from 'react-native-mask-input';
import { useForm, Controller } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import { z } from 'zod';
import { useNavigation } from '@react-navigation/native';
import { ongService } from '../../api/services/ongService';
import { colors } from '../../theme/colors';
import { typography } from '../../theme/typography';
import { spacing } from '../../theme/spacing';

const PHONE_MASK = ['(', /\d/, /\d/, ')', ' ', /\d/, /\d/, /\d/, /\d/, /\d/, '-', /\d/, /\d/, /\d/, /\d/];
const CEP_MASK = [/\d/, /\d/, /\d/, /\d/, /\d/, '-', /\d/, /\d/, /\d/];

const schema = z.object({
  nome: z.string().min(3, 'Nome obrigatório'),
  email: z.string().email('E-mail inválido'),
  descricao: z.string().optional(),
  telefone_1: z.string().refine(v => v.replace(/\D/g, '').length >= 10, 'Telefone inválido'),
  telefone_2: z.string().optional(),
  cep: z.string().refine(v => v.replace(/\D/g, '').length === 8, 'CEP inválido'),
  logradouro: z.string().min(1, 'Obrigatório'),
  numero: z.string().min(1, 'Obrigatório'),
  bairro: z.string().min(1, 'Obrigatório'),
  complemento: z.string().optional(),
  cidade: z.string().min(1, 'Obrigatório'),
  estado: z.string().min(2, 'Obrigatório'),
});

type FormData = z.infer<typeof schema>;

export function EditarPerfilOngScreen() {
  const navigation = useNavigation();
  const [carregando, setCarregando] = useState(true);
  const [salvando, setSalvando] = useState(false);
  const [cnpj, setCnpj] = useState('');
  const [buscandoCep, setBuscandoCep] = useState(false);
  const [snack, setSnack] = useState({ visible: false, msg: '', err: false });

  const { control, handleSubmit, setValue, reset, formState: { errors } } = useForm<FormData>({
    resolver: zodResolver(schema),
    defaultValues: { nome: '', email: '', descricao: '', telefone_1: '', telefone_2: '', cep: '', logradouro: '', numero: '', bairro: '', complemento: '', cidade: '', estado: '' },
  });

  const carregar = useCallback(async () => {
    setCarregando(true);
    try {
      const ong = await ongService.perfil();
      setCnpj(ong.cnpj);
      reset({
        nome: ong.nome,
        email: ong.email ?? '',
        descricao: ong.descricao ?? '',
        telefone_1: ong.telefone_1,
        telefone_2: ong.telefone_2 ?? '',
        cep: ong.cep,
        logradouro: ong.logradouro,
        numero: String(ong.numero),
        bairro: ong.bairro,
        complemento: ong.complemento ?? '',
        cidade: ong.cidade,
        estado: ong.estado,
      });
    } finally { setCarregando(false); }
  }, [reset]);

  useEffect(() => { carregar(); }, [carregar]);

  async function buscarCep(cepMasked: string) {
    const cep = cepMasked.replace(/\D/g, '');
    if (cep.length !== 8) return;
    setBuscandoCep(true);
    try {
      const res = await fetch(`https://viacep.com.br/ws/${cep}/json/`);
      const json = await res.json();
      if (!json.erro) {
        setValue('logradouro', json.logradouro ?? '');
        setValue('bairro', json.bairro ?? '');
        setValue('cidade', json.localidade ?? '');
        setValue('estado', json.uf ?? '');
      }
    } catch { } finally { setBuscandoCep(false); }
  }

  async function onSubmit(data: FormData) {
    setSalvando(true);
    try {
      await ongService.atualizarPerfil({
        nome: data.nome, email: data.email, descricao: data.descricao,
        telefone_1: data.telefone_1.replace(/\D/g, ''), telefone_2: data.telefone_2?.replace(/\D/g, ''),
        cep: data.cep.replace(/\D/g, ''), logradouro: data.logradouro, numero: Number(data.numero),
        bairro: data.bairro, complemento: data.complemento, cidade: data.cidade, estado: data.estado,
      });
      setSnack({ visible: true, msg: 'Perfil atualizado!', err: false });
      setTimeout(() => navigation.goBack(), 1200);
    } catch (err: any) {
      setSnack({ visible: true, msg: err?.message ?? 'Erro ao salvar.', err: true });
    } finally { setSalvando(false); }
  }

  if (carregando) return <View style={styles.centered}><ActivityIndicator size="large" color={colors.primary} /></View>;

  return (
    <View style={styles.root}>
      <ScrollView contentContainerStyle={styles.scroll} keyboardShouldPersistTaps="handled">
        <Secao titulo="Dados da ONG">
          <Campo label="CNPJ (não editável)">
            <View style={styles.cnpjBox}><Text style={styles.cnpjText}>{cnpj}</Text></View>
          </Campo>

          <Campo label="Nome da ONG *" error={errors.nome?.message}>
            <Controller control={control} name="nome"
              render={({ field: { onChange, onBlur, value } }) => (
                <TextInput mode="outlined" value={value} onChangeText={onChange} onBlur={onBlur}
                  outlineColor={colors.border} activeOutlineColor={colors.primary} style={styles.input} contentStyle={styles.inputContent} />
              )} />
          </Campo>

          <Campo label="E-mail *" error={errors.email?.message}>
            <Controller control={control} name="email"
              render={({ field: { onChange, onBlur, value } }) => (
                <TextInput mode="outlined" value={value} onChangeText={onChange} onBlur={onBlur}
                  keyboardType="email-address" autoCapitalize="none"
                  outlineColor={colors.border} activeOutlineColor={colors.primary} style={styles.input} contentStyle={styles.inputContent} />
              )} />
          </Campo>

          <Campo label="Descrição (opcional)">
            <Controller control={control} name="descricao"
              render={({ field: { onChange, onBlur, value } }) => (
                <TextInput mode="outlined" value={value ?? ''} onChangeText={onChange} onBlur={onBlur}
                  multiline numberOfLines={3} outlineColor={colors.border} activeOutlineColor={colors.primary}
                  style={styles.inputMultiline} contentStyle={styles.inputContent} />
              )} />
          </Campo>
        </Secao>

        <Secao titulo="Contato">
          <Campo label="Telefone 1 *" error={errors.telefone_1?.message}>
            <Controller control={control} name="telefone_1"
              render={({ field: { onChange, value } }) => (
                <MaskInput value={value} onChangeText={onChange} mask={PHONE_MASK} style={styles.maskInput} keyboardType="phone-pad" />
              )} />
          </Campo>

          <Campo label="Telefone 2 (opcional)">
            <Controller control={control} name="telefone_2"
              render={({ field: { onChange, value } }) => (
                <MaskInput value={value ?? ''} onChangeText={onChange} mask={PHONE_MASK} style={styles.maskInput} keyboardType="phone-pad" />
              )} />
          </Campo>
        </Secao>

        <Secao titulo="Endereço">
          <Campo label="CEP *" error={errors.cep?.message}>
            <Controller control={control} name="cep"
              render={({ field: { onChange, value } }) => (
                <View>
                  <MaskInput value={value} onChangeText={(m) => { onChange(m); buscarCep(m); }} mask={CEP_MASK} style={styles.maskInput} keyboardType="numeric" />
                  {buscandoCep && <Text style={styles.hint}>Buscando endereço...</Text>}
                </View>
              )} />
          </Campo>

          {(['logradouro', 'numero', 'bairro', 'complemento', 'cidade'] as const).map(field => (
            <Campo key={field} label={field.charAt(0).toUpperCase() + field.slice(1)} error={(errors as any)[field]?.message}>
              <Controller control={control} name={field}
                render={({ field: { onChange, onBlur, value } }) => (
                  <TextInput mode="outlined" value={value ?? ''} onChangeText={onChange} onBlur={onBlur}
                    keyboardType={field === 'numero' ? 'numeric' : 'default'}
                    outlineColor={colors.border} activeOutlineColor={colors.primary} style={styles.input} contentStyle={styles.inputContent} />
                )} />
            </Campo>
          ))}

          <Campo label="Estado (UF) *" error={errors.estado?.message}>
            <Controller control={control} name="estado"
              render={({ field: { onChange, onBlur, value } }) => (
                <TextInput mode="outlined" value={value} onChangeText={onChange} onBlur={onBlur}
                  maxLength={2} autoCapitalize="characters"
                  outlineColor={colors.border} activeOutlineColor={colors.primary} style={styles.input} contentStyle={styles.inputContent} />
              )} />
          </Campo>
        </Secao>

        <TouchableOpacity style={[styles.btnSalvar, salvando && styles.btnDisabled]}
          onPress={handleSubmit(onSubmit)} disabled={salvando} activeOpacity={0.85}>
          {salvando ? <ActivityIndicator color={colors.white} /> : <Text style={styles.btnLabel}>Salvar alterações</Text>}
        </TouchableOpacity>
      </ScrollView>

      <Snackbar visible={snack.visible} onDismiss={() => setSnack(s => ({ ...s, visible: false }))}
        duration={3000} style={{ backgroundColor: snack.err ? colors.error : colors.success }}>
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
  centered: { flex: 1, alignItems: 'center', justifyContent: 'center' },
  secao: { backgroundColor: colors.bg, borderRadius: 12, padding: spacing.md, marginBottom: spacing.md },
  secaoTitulo: {
    fontFamily: typography.fontFamily.titleBold, fontSize: typography.fontSize.md,
    color: colors.text, marginBottom: spacing.sm,
    borderLeftWidth: 3, borderLeftColor: colors.primary, paddingLeft: spacing.sm,
  },
  campo: { marginBottom: spacing.sm },
  campoLabel: { fontFamily: typography.fontFamily.body, fontSize: typography.fontSize.sm, color: colors.secondary, marginBottom: 4 },
  campoErro: { fontFamily: typography.fontFamily.body, fontSize: typography.fontSize.xs, color: colors.error, marginTop: 2 },
  input: { backgroundColor: colors.bg, height: 48 },
  inputMultiline: { backgroundColor: colors.bg },
  inputContent: { fontFamily: typography.fontFamily.body, fontSize: typography.fontSize.md },
  maskInput: { fontFamily: typography.fontFamily.body, fontSize: typography.fontSize.md, color: colors.text, borderWidth: 1, borderColor: colors.border, borderRadius: 4, paddingHorizontal: spacing.md, paddingVertical: spacing.sm, backgroundColor: colors.bg },
  hint: { fontFamily: typography.fontFamily.body, fontSize: typography.fontSize.xs, color: colors.secondary, marginTop: 2 },
  cnpjBox: { backgroundColor: colors.bgMuted, borderRadius: 8, padding: spacing.sm, borderWidth: 1, borderColor: colors.border },
  cnpjText: { fontFamily: typography.fontFamily.body, fontSize: typography.fontSize.md, color: colors.secondary },
  btnSalvar: { backgroundColor: colors.primary, borderRadius: 10, paddingVertical: spacing.md, alignItems: 'center', marginTop: spacing.sm },
  btnDisabled: { backgroundColor: colors.border },
  btnLabel: { fontFamily: typography.fontFamily.bodyBold, fontSize: typography.fontSize.md, color: colors.white },
});
