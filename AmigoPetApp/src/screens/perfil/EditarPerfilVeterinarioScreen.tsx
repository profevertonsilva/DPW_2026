import React, { useCallback, useEffect, useState } from 'react';
import {
  View, Text, ScrollView, StyleSheet, TouchableOpacity, ActivityIndicator, Switch,
} from 'react-native';
import { TextInput, Snackbar } from 'react-native-paper';
import MaskInput from 'react-native-mask-input';
import { useForm, Controller } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import { z } from 'zod';
import { useNavigation } from '@react-navigation/native';
import { vetService } from '../../api/services/vetService';
import { clinicaService } from '../../api/services/clinicaService';
import { colors } from '../../theme/colors';
import { typography } from '../../theme/typography';
import { spacing } from '../../theme/spacing';
import type { Clinica } from '../../types/Clinica';

const PHONE_MASK = ['(', /\d/, /\d/, ')', ' ', /\d/, /\d/, /\d/, /\d/, /\d/, '-', /\d/, /\d/, /\d/, /\d/];
const CEP_MASK = [/\d/, /\d/, /\d/, /\d/, /\d/, '-', /\d/, /\d/, /\d/];

const schema = z.object({
  nome: z.string().min(3, 'Nome obrigatório'),
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

export function EditarPerfilVeterinarioScreen() {
  const navigation = useNavigation();
  const [carregando, setCarregando] = useState(true);
  const [salvando, setSalvando] = useState(false);
  const [crmv, setCrmv] = useState('');
  const [todasClinicas, setTodasClinicas] = useState<Clinica[]>([]);
  const [clinicasAssociadas, setClinicasAssociadas] = useState<Set<number>>(new Set());
  const [buscandoCep, setBuscandoCep] = useState(false);
  const [snack, setSnack] = useState({ visible: false, msg: '', err: false });

  const { control, handleSubmit, setValue, reset, formState: { errors } } = useForm<FormData>({
    resolver: zodResolver(schema),
    defaultValues: { nome: '', telefone_1: '', telefone_2: '', cep: '', logradouro: '', numero: '', bairro: '', complemento: '', cidade: '', estado: '' },
  });

  const carregar = useCallback(async () => {
    setCarregando(true);
    try {
      const [perfil, minhas, todas] = await Promise.all([
        vetService.perfil(),
        vetService.minhasClinicas(),
        clinicaService.listar(),
      ]);
      setCrmv(perfil.crmv);
      reset({ nome: perfil.nome, telefone_1: perfil.telefone_1, telefone_2: perfil.telefone_2 ?? '', cep: perfil.cep, logradouro: perfil.logradouro, numero: String(perfil.numero), bairro: perfil.bairro, complemento: perfil.complemento ?? '', cidade: perfil.cidade, estado: perfil.estado });
      setTodasClinicas(todas);
      setClinicasAssociadas(new Set(minhas.map(c => c.id)));
    } finally { setCarregando(false); }
  }, [reset]);

  useEffect(() => { carregar(); }, [carregar]);

  async function toggleClinica(clinicaId: number, atual: boolean) {
    try {
      if (atual) {
        await vetService.desassociarClinica(clinicaId);
        setClinicasAssociadas(prev => { const s = new Set(prev); s.delete(clinicaId); return s; });
      } else {
        await vetService.associarClinica(clinicaId);
        setClinicasAssociadas(prev => new Set(prev).add(clinicaId));
      }
    } catch { setSnack({ visible: true, msg: 'Erro ao atualizar clínica.', err: true }); }
  }

  async function buscarCep(cepMasked: string) {
    const cep = cepMasked.replace(/\D/g, '');
    if (cep.length !== 8) return;
    setBuscandoCep(true);
    try {
      const res = await fetch(`https://viacep.com.br/ws/${cep}/json/`);
      const json = await res.json();
      if (!json.erro) { setValue('logradouro', json.logradouro ?? ''); setValue('bairro', json.bairro ?? ''); setValue('cidade', json.localidade ?? ''); setValue('estado', json.uf ?? ''); }
    } catch { } finally { setBuscandoCep(false); }
  }

  async function onSubmit(data: FormData) {
    setSalvando(true);
    try {
      await vetService.atualizarPerfil({
        nome: data.nome, telefone_1: data.telefone_1.replace(/\D/g, ''), telefone_2: data.telefone_2?.replace(/\D/g, ''),
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
        <Secao titulo="Dados do Profissional">
          <View style={styles.crmvBox}>
            <Text style={styles.crmvLabel}>CRMV (não editável)</Text>
            <Text style={styles.crmvValue}>{crmv}</Text>
          </View>

          <Campo label="Nome *" error={errors.nome?.message}>
            <Controller control={control} name="nome"
              render={({ field: { onChange, onBlur, value } }) => (
                <TextInput mode="outlined" value={value} onChangeText={onChange} onBlur={onBlur}
                  outlineColor={colors.border} activeOutlineColor={colors.primary} style={styles.input} contentStyle={styles.inputContent} />
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

        {/* Clínicas associadas */}
        <View style={styles.clinicasSecao}>
          <Text style={styles.clinicasTitulo}>Minhas Clínicas</Text>
          <Text style={styles.clinicasSub}>Ative para associar uma clínica ao seu perfil.</Text>
          {todasClinicas.map(c => (
            <View key={c.id} style={styles.clinicaRow}>
              <View style={styles.clinicaInfo}>
                <Text style={styles.clinicaNome}>{c.nome}</Text>
                <Text style={styles.clinicaCidade}>{c.cidade}, {c.estado}</Text>
              </View>
              <Switch
                value={clinicasAssociadas.has(c.id)}
                onValueChange={(v) => toggleClinica(c.id, clinicasAssociadas.has(c.id))}
                trackColor={{ true: colors.primary, false: colors.border }}
                thumbColor={clinicasAssociadas.has(c.id) ? colors.white : '#f4f3f4'}
              />
            </View>
          ))}
        </View>

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
  campo: { marginBottom: spacing.sm },
  campoLabel: { fontFamily: typography.fontFamily.body, fontSize: typography.fontSize.sm, color: colors.secondary, marginBottom: 4 },
  campoErro: { fontFamily: typography.fontFamily.body, fontSize: typography.fontSize.xs, color: colors.error, marginTop: 2 },
  input: { backgroundColor: colors.bg, height: 48 },
  inputMultiline: { backgroundColor: colors.bg },
  inputContent: { fontFamily: typography.fontFamily.body, fontSize: typography.fontSize.md },
  maskInput: { fontFamily: typography.fontFamily.body, fontSize: typography.fontSize.md, color: colors.text, borderWidth: 1, borderColor: colors.border, borderRadius: 4, paddingHorizontal: spacing.md, paddingVertical: spacing.sm, backgroundColor: colors.bg },
  hint: { fontFamily: typography.fontFamily.body, fontSize: typography.fontSize.xs, color: colors.secondary, marginTop: 2 },
  crmvBox: { backgroundColor: colors.bgMuted, borderRadius: 8, padding: spacing.sm, marginBottom: spacing.sm, borderWidth: 1, borderColor: colors.border },
  crmvLabel: { fontFamily: typography.fontFamily.body, fontSize: typography.fontSize.xs, color: colors.secondary },
  crmvValue: { fontFamily: typography.fontFamily.bodyBold, fontSize: typography.fontSize.md, color: colors.text, marginTop: 2 },
  clinicasSecao: { backgroundColor: colors.bg, borderRadius: 12, padding: spacing.md, marginBottom: spacing.md },
  clinicasTitulo: { fontFamily: typography.fontFamily.titleBold, fontSize: typography.fontSize.md, color: colors.text, marginBottom: 4 },
  clinicasSub: { fontFamily: typography.fontFamily.body, fontSize: typography.fontSize.xs, color: colors.secondary, marginBottom: spacing.sm },
  clinicaRow: { flexDirection: 'row', alignItems: 'center', paddingVertical: spacing.sm, borderBottomWidth: 1, borderBottomColor: colors.border },
  clinicaInfo: { flex: 1 },
  clinicaNome: { fontFamily: typography.fontFamily.bodyBold, fontSize: typography.fontSize.sm, color: colors.text },
  clinicaCidade: { fontFamily: typography.fontFamily.body, fontSize: typography.fontSize.xs, color: colors.secondary },
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
