import React, { useEffect, useState } from 'react';
import {
  View, Text, ScrollView, StyleSheet, TouchableOpacity, ActivityIndicator, Image, Alert,
} from 'react-native';
import { TextInput, Snackbar } from 'react-native-paper';
import MaskInput from 'react-native-mask-input';
import * as ImagePicker from 'expo-image-picker';
import { useForm, Controller } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import { z } from 'zod';
import { useNavigation } from '@react-navigation/native';
import type { NativeStackNavigationProp } from '@react-navigation/native-stack';
import { animalService } from '../../api/services/animalService';
import { PermissionGate } from '../../components/ui/PermissionGate';
import { colors } from '../../theme/colors';
import { typography } from '../../theme/typography';
import { spacing } from '../../theme/spacing';
import type { MeusAnimaisStackParamList } from '../../navigation/stacks/MeusAnimaisStack';
import type { AnimalPorte, AnimalSexo, Especie } from '../../types/Animal';

const CEP_MASK = [/\d/, /\d/, /\d/, /\d/, /\d/, '-', /\d/, /\d/, /\d/];
type Nav = NativeStackNavigationProp<MeusAnimaisStackParamList, 'CadastrarAnimal'>;

const schema = z.object({
  nome: z.string().min(2, 'Nome deve ter ao menos 2 caracteres'),
  fk_especie_id: z.number({ error: 'Selecione a espécie' }).int().positive('Selecione a espécie'),
  raca: z.string().optional(),
  cor: z.string().optional(),
  sexo: z.enum(['m', 'f'], { error: 'Selecione o sexo' }),
  data_nascimento: z.string().optional(),
  porte: z.enum(['pequeno', 'medio', 'grande', 'gigante'], { error: 'Selecione o porte' }),
  descricao: z.string().optional(),
  historico_resgate: z.string().optional(),
  alergias: z.string().optional(),
  castrado: z.boolean(),
  obs_veterinarias: z.string().optional(),
  cep: z.string().optional(),
  logradouro: z.string().optional(),
  numero: z.string().optional(),
  bairro: z.string().optional(),
  cidade: z.string().optional(),
  estado: z.string().optional(),
});

type FormData = z.infer<typeof schema>;

// Espécies carregadas dinamicamente da API
const PORTES: { label: string; value: AnimalPorte }[] = [
  { label: 'Pequeno', value: 'pequeno' },
  { label: 'Médio', value: 'medio' },
  { label: 'Grande', value: 'grande' },
  { label: 'Gigante', value: 'gigante' },
];
const SEXOS: { label: string; value: AnimalSexo }[] = [
  { label: 'Macho', value: 'm' },
  { label: 'Fêmea', value: 'f' },
];

const DATE_MASK = [/\d/, /\d/, '/', /\d/, /\d/, '/', /\d/, /\d/, /\d/, /\d/];

function parseDateToISO(br: string) {
  const [dia, mes, ano] = br.split('/');
  return `${ano}-${mes}-${dia}`;
}

function CadastrarAnimalForm() {
  const navigation = useNavigation<Nav>();
  const [enviando, setEnviando] = useState(false);
  const [buscandoCep, setBuscandoCep] = useState(false);
  const [fotos, setFotos] = useState<string[]>([]);
  const [snack, setSnack] = useState({ visible: false, msg: '' });
  const [especies, setEspecies] = useState<Especie[]>([]);

  useEffect(() => {
    animalService.especies().then(setEspecies).catch(() => {});
  }, []);

  const { control, handleSubmit, setValue, watch, formState: { errors } } = useForm<FormData>({
    resolver: zodResolver(schema),
    defaultValues: {
      nome: '', fk_especie_id: undefined as any, raca: '', cor: '', sexo: undefined as any, data_nascimento: '',
      porte: undefined as any, descricao: '', historico_resgate: '', alergias: '',
      castrado: false, obs_veterinarias: '',
      cep: '', logradouro: '', numero: '', bairro: '', cidade: '', estado: '',
    },
  });

  const especieSelecionada = watch('sexo');
  void especieSelecionada;

  async function escolherFoto() {
    if (fotos.length >= 5) {
      Alert.alert('Limite atingido', 'Máximo de 5 fotos por animal.');
      return;
    }
    const result = await ImagePicker.launchImageLibraryAsync({
      mediaTypes: ['images'],
      quality: 0.7,
      allowsEditing: true,
      aspect: [4, 3],
    });
    if (!result.canceled && result.assets[0]) {
      setFotos(prev => [...prev, result.assets[0].uri]);
    }
  }

  function removerFoto(idx: number) {
    setFotos(prev => prev.filter((_, i) => i !== idx));
  }

  async function buscarCep(cepMasked: string) {
    const cep = cepMasked.replace(/\D/g, '');
    if (cep.length !== 8) return;
    setBuscandoCep(true);
    try {
      const res = await fetch(`https://viacep.com.br/ws/${cep}/json/`);
      const json = await res.json();
      if (json.erro) return;
      setValue('logradouro', json.logradouro ?? '');
      setValue('bairro', json.bairro ?? '');
      setValue('cidade', json.localidade ?? '');
      setValue('estado', json.uf ?? '');
    } catch { /* ViaCEP indisponível */ } finally { setBuscandoCep(false); }
  }

  async function onSubmit(data: FormData) {
    setEnviando(true);
    try {
      await animalService.cadastrar({
        nome: data.nome,
        fk_especie_id: data.fk_especie_id,
        raca: data.raca,
        cor: data.cor,
        sexo: data.sexo,
        data_nascimento: data.data_nascimento ? parseDateToISO(data.data_nascimento) : null,
        porte: data.porte,
        descricao: data.descricao,
        historico_resgate: data.historico_resgate,
        fotos,
        alergias: data.alergias,
        castrado: data.castrado,
        obs_veterinarias: data.obs_veterinarias,
        local_cep: data.cep?.replace(/\D/g, ''),
        local_logradouro: data.logradouro,
        local_numero: data.numero,
        local_bairro: data.bairro,
        local_cidade: data.cidade,
        local_estado: data.estado,
      });
      navigation.goBack();
    } catch (err: any) {
      const msg = err?.response?.data?.erro ?? err?.message ?? 'Erro ao cadastrar animal.';
      setSnack({ visible: true, msg });
    } finally { setEnviando(false); }
  }

  return (
    <View style={styles.root}>
      <ScrollView contentContainerStyle={styles.scroll} keyboardShouldPersistTaps="handled">

        <Secao titulo="Identificação">
          <Campo label="Nome do animal" error={errors.nome?.message}>
            <Controller control={control} name="nome"
              render={({ field: { onChange, onBlur, value } }) => (
                <TextInput mode="outlined" value={value} onChangeText={onChange} onBlur={onBlur}
                  autoCapitalize="words" outlineColor={colors.border} activeOutlineColor={colors.primary}
                  style={styles.input} contentStyle={styles.inputContent} />
              )} />
          </Campo>

          <Campo label="Espécie" error={errors.fk_especie_id?.message}>
            <Controller control={control} name="fk_especie_id"
              render={({ field: { onChange, value } }) => (
                <View style={styles.chips}>
                  {especies.map(e => (
                    <TouchableOpacity key={e.id}
                      style={[styles.chip, value === e.id && styles.chipAtivo]}
                      onPress={() => onChange(e.id)}>
                      <Text style={[styles.chipLabel, value === e.id && styles.chipLabelAtivo]}>{e.nome}</Text>
                    </TouchableOpacity>
                  ))}
                </View>
              )} />
          </Campo>

          <Campo label="Raça (opcional)" error={errors.raca?.message}>
            <Controller control={control} name="raca"
              render={({ field: { onChange, onBlur, value } }) => (
                <TextInput mode="outlined" value={value ?? ''} onChangeText={onChange} onBlur={onBlur}
                  outlineColor={colors.border} activeOutlineColor={colors.primary}
                  style={styles.input} contentStyle={styles.inputContent} />
              )} />
          </Campo>

          <Campo label="Cor/pelagem (opcional)" error={errors.cor?.message}>
            <Controller control={control} name="cor"
              render={({ field: { onChange, onBlur, value } }) => (
                <TextInput mode="outlined" value={value ?? ''} onChangeText={onChange} onBlur={onBlur}
                  outlineColor={colors.border} activeOutlineColor={colors.primary}
                  style={styles.input} contentStyle={styles.inputContent} />
              )} />
          </Campo>
        </Secao>

        <Secao titulo="Características">
          <Campo label="Sexo" error={errors.sexo?.message}>
            <Controller control={control} name="sexo"
              render={({ field: { onChange, value } }) => (
                <View style={styles.chips}>
                  {SEXOS.map(s => (
                    <TouchableOpacity key={s.value}
                      style={[styles.chip, value === s.value && styles.chipAtivo]}
                      onPress={() => onChange(s.value)}>
                      <Text style={[styles.chipLabel, value === s.value && styles.chipLabelAtivo]}>{s.label}</Text>
                    </TouchableOpacity>
                  ))}
                </View>
              )} />
          </Campo>

          <Campo label="Porte" error={errors.porte?.message}>
            <Controller control={control} name="porte"
              render={({ field: { onChange, value } }) => (
                <View style={styles.chips}>
                  {PORTES.map(p => (
                    <TouchableOpacity key={p.value}
                      style={[styles.chip, value === p.value && styles.chipAtivo]}
                      onPress={() => onChange(p.value)}>
                      <Text style={[styles.chipLabel, value === p.value && styles.chipLabelAtivo]}>{p.label}</Text>
                    </TouchableOpacity>
                  ))}
                </View>
              )} />
          </Campo>

          <Campo label="Data de nascimento (DD/MM/AAAA, opcional)" error={errors.data_nascimento?.message}>
            <Controller control={control} name="data_nascimento"
              render={({ field: { onChange, value } }) => (
                <MaskInput value={value ?? ''} onChangeText={onChange} mask={DATE_MASK}
                  style={styles.maskInput} keyboardType="numeric" />
              )} />
          </Campo>
        </Secao>

        <Secao titulo="Fotos (até 5)">
          <View style={styles.fotosGrid}>
            {fotos.map((uri, idx) => (
              <View key={idx} style={styles.fotoContainer}>
                <Image source={{ uri }} style={styles.fotoThumb} />
                <TouchableOpacity style={styles.fotoRemover} onPress={() => removerFoto(idx)}>
                  <Text style={styles.fotoRemoverLabel}>✕</Text>
                </TouchableOpacity>
              </View>
            ))}
            {fotos.length < 5 && (
              <TouchableOpacity style={styles.fotoAdicionar} onPress={escolherFoto}>
                <Text style={styles.fotoAdicionarLabel}>+</Text>
                <Text style={styles.fotoAdicionarSub}>Foto</Text>
              </TouchableOpacity>
            )}
          </View>
        </Secao>

        <Secao titulo="Descrição">
          <Campo label="Descrição (opcional)">
            <Controller control={control} name="descricao"
              render={({ field: { onChange, onBlur, value } }) => (
                <TextInput mode="outlined" value={value ?? ''} onChangeText={onChange} onBlur={onBlur}
                  multiline numberOfLines={3} outlineColor={colors.border} activeOutlineColor={colors.primary}
                  style={styles.inputMultiline} contentStyle={styles.inputContent} />
              )} />
          </Campo>

          <Campo label="Histórico de resgate (opcional)">
            <Controller control={control} name="historico_resgate"
              render={({ field: { onChange, onBlur, value } }) => (
                <TextInput mode="outlined" value={value ?? ''} onChangeText={onChange} onBlur={onBlur}
                  multiline numberOfLines={3} outlineColor={colors.border} activeOutlineColor={colors.primary}
                  style={styles.inputMultiline} contentStyle={styles.inputContent} />
              )} />
          </Campo>
        </Secao>

        <Secao titulo="Saúde">
          <Campo label="Castrado?">
            <Controller control={control} name="castrado"
              render={({ field: { onChange, value } }) => (
                <View style={styles.chips}>
                  <TouchableOpacity style={[styles.chip, value && styles.chipAtivo]} onPress={() => onChange(true)}>
                    <Text style={[styles.chipLabel, value && styles.chipLabelAtivo]}>Sim</Text>
                  </TouchableOpacity>
                  <TouchableOpacity style={[styles.chip, !value && styles.chipAtivo]} onPress={() => onChange(false)}>
                    <Text style={[styles.chipLabel, !value && styles.chipLabelAtivo]}>Não</Text>
                  </TouchableOpacity>
                </View>
              )} />
          </Campo>

          <Campo label="Alergias (opcional)">
            <Controller control={control} name="alergias"
              render={({ field: { onChange, onBlur, value } }) => (
                <TextInput mode="outlined" value={value ?? ''} onChangeText={onChange} onBlur={onBlur}
                  outlineColor={colors.border} activeOutlineColor={colors.primary}
                  style={styles.input} contentStyle={styles.inputContent} />
              )} />
          </Campo>

          <Campo label="Observações veterinárias (opcional)">
            <Controller control={control} name="obs_veterinarias"
              render={({ field: { onChange, onBlur, value } }) => (
                <TextInput mode="outlined" value={value ?? ''} onChangeText={onChange} onBlur={onBlur}
                  multiline numberOfLines={3} outlineColor={colors.border} activeOutlineColor={colors.primary}
                  style={styles.inputMultiline} contentStyle={styles.inputContent} />
              )} />
          </Campo>
        </Secao>

        <Secao titulo="Localização (opcional)">
          <Campo label="CEP">
            <Controller control={control} name="cep"
              render={({ field: { onChange, value } }) => (
                <View>
                  <MaskInput value={value ?? ''}
                    onChangeText={(m) => { onChange(m); buscarCep(m); }}
                    mask={CEP_MASK} style={styles.maskInput} keyboardType="numeric" />
                  {buscandoCep && <Text style={styles.hintText}>Buscando endereço...</Text>}
                </View>
              )} />
          </Campo>

          <Campo label="Logradouro">
            <Controller control={control} name="logradouro"
              render={({ field: { onChange, onBlur, value } }) => (
                <TextInput mode="outlined" value={value ?? ''} onChangeText={onChange} onBlur={onBlur}
                  outlineColor={colors.border} activeOutlineColor={colors.primary}
                  style={styles.input} contentStyle={styles.inputContent} />
              )} />
          </Campo>

          <View style={styles.row}>
            <View style={styles.rowNumero}>
              <Campo label="Número">
                <Controller control={control} name="numero"
                  render={({ field: { onChange, onBlur, value } }) => (
                    <TextInput mode="outlined" value={value ?? ''} onChangeText={onChange} onBlur={onBlur}
                      keyboardType="numeric" outlineColor={colors.border} activeOutlineColor={colors.primary}
                      style={styles.input} contentStyle={styles.inputContent} />
                  )} />
              </Campo>
            </View>
            <View style={styles.rowBairro}>
              <Campo label="Bairro">
                <Controller control={control} name="bairro"
                  render={({ field: { onChange, onBlur, value } }) => (
                    <TextInput mode="outlined" value={value ?? ''} onChangeText={onChange} onBlur={onBlur}
                      outlineColor={colors.border} activeOutlineColor={colors.primary}
                      style={styles.input} contentStyle={styles.inputContent} />
                  )} />
              </Campo>
            </View>
          </View>

          <View style={styles.row}>
            <View style={styles.rowCidade}>
              <Campo label="Cidade">
                <Controller control={control} name="cidade"
                  render={({ field: { onChange, onBlur, value } }) => (
                    <TextInput mode="outlined" value={value ?? ''} onChangeText={onChange} onBlur={onBlur}
                      outlineColor={colors.border} activeOutlineColor={colors.primary}
                      style={styles.input} contentStyle={styles.inputContent} />
                  )} />
              </Campo>
            </View>
            <View style={styles.rowUF}>
              <Campo label="UF">
                <Controller control={control} name="estado"
                  render={({ field: { onChange, onBlur, value } }) => (
                    <TextInput mode="outlined" value={value ?? ''} onChangeText={onChange} onBlur={onBlur}
                      maxLength={2} autoCapitalize="characters"
                      outlineColor={colors.border} activeOutlineColor={colors.primary}
                      style={styles.input} contentStyle={styles.inputContent} />
                  )} />
              </Campo>
            </View>
          </View>
        </Secao>

        <TouchableOpacity
          style={[styles.btnCriar, enviando && styles.btnDisabled]}
          onPress={handleSubmit(onSubmit)}
          disabled={enviando}
          activeOpacity={0.85}
        >
          {enviando
            ? <ActivityIndicator color={colors.white} />
            : <Text style={styles.btnLabel}>Cadastrar animal</Text>}
        </TouchableOpacity>
      </ScrollView>

      <Snackbar
        visible={snack.visible}
        onDismiss={() => setSnack(s => ({ ...s, visible: false }))}
        duration={4000}
        style={{ backgroundColor: colors.error }}
      >
        {snack.msg}
      </Snackbar>
    </View>
  );
}

export function CadastrarAnimalScreen() {
  return (
    <PermissionGate
      capability="podeCadastrarAnimal"
      fallback={
        <View style={styles.semPermissao}>
          <Text style={styles.semPermissaoTexto}>Apenas ONGs podem cadastrar animais.</Text>
        </View>
      }
    >
      <CadastrarAnimalForm />
    </PermissionGate>
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
  maskInput: {
    fontFamily: typography.fontFamily.body, fontSize: typography.fontSize.md, color: colors.text,
    borderWidth: 1, borderColor: colors.border, borderRadius: 4,
    paddingHorizontal: spacing.md, paddingVertical: spacing.sm, backgroundColor: colors.bg,
  },
  hintText: { fontFamily: typography.fontFamily.body, fontSize: typography.fontSize.xs, color: colors.secondary, marginTop: 2 },
  chips: { flexDirection: 'row', flexWrap: 'wrap', gap: spacing.xs, marginTop: 4 },
  chip: {
    borderWidth: 1, borderColor: colors.border, borderRadius: 20,
    paddingVertical: 6, paddingHorizontal: spacing.md,
  },
  chipAtivo: { backgroundColor: colors.primary, borderColor: colors.primary },
  chipLabel: { fontFamily: typography.fontFamily.body, fontSize: typography.fontSize.sm, color: colors.text },
  chipLabelAtivo: { color: colors.white },
  fotosGrid: { flexDirection: 'row', flexWrap: 'wrap', gap: spacing.sm },
  fotoContainer: { position: 'relative', width: 80, height: 80 },
  fotoThumb: { width: 80, height: 80, borderRadius: 8 },
  fotoRemover: {
    position: 'absolute', top: -6, right: -6,
    backgroundColor: colors.error, borderRadius: 10, width: 20, height: 20, alignItems: 'center', justifyContent: 'center',
  },
  fotoRemoverLabel: { color: colors.white, fontSize: 10, fontFamily: typography.fontFamily.bodyBold },
  fotoAdicionar: {
    width: 80, height: 80, borderRadius: 8,
    borderWidth: 2, borderColor: colors.border, borderStyle: 'dashed',
    alignItems: 'center', justifyContent: 'center',
  },
  fotoAdicionarLabel: { fontSize: 24, color: colors.secondary },
  fotoAdicionarSub: { fontFamily: typography.fontFamily.body, fontSize: typography.fontSize.xs, color: colors.secondary },
  row: { flexDirection: 'row', gap: spacing.sm },
  rowNumero: { flex: 1 },
  rowBairro: { flex: 2 },
  rowCidade: { flex: 2 },
  rowUF: { flex: 1 },
  btnCriar: { backgroundColor: colors.primary, borderRadius: 10, paddingVertical: spacing.md, alignItems: 'center', marginTop: spacing.sm },
  btnDisabled: { backgroundColor: colors.border },
  btnLabel: { fontFamily: typography.fontFamily.bodyBold, fontSize: typography.fontSize.md, color: colors.white },
  semPermissao: { flex: 1, alignItems: 'center', justifyContent: 'center', padding: spacing.lg },
  semPermissaoTexto: { fontFamily: typography.fontFamily.body, fontSize: typography.fontSize.md, color: colors.secondary, textAlign: 'center' },
});
