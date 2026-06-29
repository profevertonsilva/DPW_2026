import React, { useState } from 'react';
import {
  View, Text, ScrollView, StyleSheet, TouchableOpacity, ActivityIndicator, Image, Alert,
} from 'react-native';
import { TextInput, Snackbar } from 'react-native-paper';
import MaskInput from 'react-native-mask-input';
import * as ImagePicker from 'expo-image-picker';
import * as Location from 'expo-location';
import { useForm, Controller } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import { z } from 'zod';
import { useNavigation } from '@react-navigation/native';
import type { NativeStackNavigationProp } from '@react-navigation/native-stack';
import { avistamentoService } from '../../api/services/avistamentoService';
import { useAuth } from '../../hooks/useAuth';
import { colors } from '../../theme/colors';
import { typography } from '../../theme/typography';
import { spacing } from '../../theme/spacing';
import type { AppStackParamList } from '../../navigation/RootNavigator';
import type { AvistamentoEspecie } from '../../types/Avistamento';

type Nav = NativeStackNavigationProp<AppStackParamList, 'ReportarAnimal'>;

const CEP_MASK = [/\d/, /\d/, /\d/, /\d/, /\d/, '-', /\d/, /\d/, /\d/];
const DATE_MASK = [/\d/, /\d/, '/', /\d/, /\d/, '/', /\d/, /\d/, /\d/, /\d/];
const TIME_MASK = [/\d/, /\d/, ':', /\d/, /\d/];

type ModoLocalizacao = 'gps' | 'manual';

const schema = z.object({
  data: z.string().min(10, 'Data inválida'),
  hora: z.string().min(5, 'Hora inválida'),
  condicao: z.string().min(3, 'Descreva a condição do animal'),
  acoes_tomadas: z.string().min(3, 'Descreva as ações tomadas'),
  especie: z.enum(['cachorro', 'gato', 'outro', 'nao_sei'], { error: 'Selecione a espécie' }),
  cep: z.string().optional(),
  logradouro: z.string().optional(),
  numero: z.string().optional(),
  bairro: z.string().optional(),
  cidade: z.string().optional(),
  estado: z.string().optional(),
});

type FormData = z.infer<typeof schema>;

const ESPECIES: { label: string; value: AvistamentoEspecie }[] = [
  { label: 'Cachorro', value: 'cachorro' },
  { label: 'Gato', value: 'gato' },
  { label: 'Outro', value: 'outro' },
  { label: 'Não sei', value: 'nao_sei' },
];

function now() {
  const d = new Date();
  const pad = (n: number) => String(n).padStart(2, '0');
  return {
    data: `${pad(d.getDate())}/${pad(d.getMonth() + 1)}/${d.getFullYear()}`,
    hora: `${pad(d.getHours())}:${pad(d.getMinutes())}`,
    iso: d.toISOString(),
  };
}

function toISO(dataBR: string, hora: string): string {
  const [dia, mes, ano] = dataBR.split('/');
  return `${ano}-${mes}-${dia}T${hora}:00.000Z`;
}

export function ReportarAnimalScreen() {
  const { user } = useAuth();
  const navigation = useNavigation<Nav>();
  const [enviando, setEnviando] = useState(false);
  const [fotos, setFotos] = useState<string[]>([]);
  const [modoLocal, setModoLocal] = useState<ModoLocalizacao>('gps');
  const [gpsCarregando, setGpsCarregando] = useState(false);
  const [lat, setLat] = useState<number | null>(null);
  const [lng, setLng] = useState<number | null>(null);
  const [enderecoGps, setEnderecoGps] = useState<string | null>(null);
  const [buscandoCep, setBuscandoCep] = useState(false);
  const [snack, setSnack] = useState({ visible: false, msg: '' });

  const { control, handleSubmit, setValue, watch, formState: { errors } } = useForm<FormData>({
    resolver: zodResolver(schema),
    defaultValues: {
      data: now().data,
      hora: now().hora,
      condicao: '',
      acoes_tomadas: '',
      especie: undefined as any,
      cep: '', logradouro: '', numero: '', bairro: '', cidade: '', estado: '',
    },
  });

  async function usarGPS() {
    setGpsCarregando(true);
    try {
      const { status } = await Location.requestForegroundPermissionsAsync();
      if (status !== 'granted') {
        Alert.alert('Permissão negada', 'O acesso à localização foi negado. Use o endereço manual.');
        setModoLocal('manual');
        return;
      }
      const loc = await Location.getCurrentPositionAsync({ accuracy: Location.Accuracy.High });
      setLat(loc.coords.latitude);
      setLng(loc.coords.longitude);

      const [geo] = await Location.reverseGeocodeAsync({
        latitude: loc.coords.latitude,
        longitude: loc.coords.longitude,
      });

      if (geo) {
        const partes = [geo.street, geo.district, geo.city, geo.region].filter(Boolean);
        setEnderecoGps(partes.join(', '));
      } else {
        setEnderecoGps(`${loc.coords.latitude.toFixed(5)}, ${loc.coords.longitude.toFixed(5)}`);
      }
    } catch {
      Alert.alert('Erro', 'Não foi possível obter localização. Tente o endereço manual.');
    } finally {
      setGpsCarregando(false);
    }
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

  async function escolherFoto() {
    if (fotos.length >= 5) { Alert.alert('Limite', 'Máximo de 5 fotos.'); return; }
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

  async function tirarFoto() {
    if (fotos.length >= 5) { Alert.alert('Limite', 'Máximo de 5 fotos.'); return; }
    const permResult = await ImagePicker.requestCameraPermissionsAsync();
    if (!permResult.granted) { Alert.alert('Permissão negada', 'Permita acesso à câmera.'); return; }
    const result = await ImagePicker.launchCameraAsync({
      mediaTypes: ['images'],
      quality: 0.7,
      allowsEditing: true,
      aspect: [4, 3],
    });
    if (!result.canceled && result.assets[0]) {
      setFotos(prev => [...prev, result.assets[0].uri]);
    }
  }

  async function onSubmit(data: FormData) {
    if (modoLocal === 'gps' && !lat) {
      Alert.alert('Localização', 'Clique em "Usar minha localização" ou troque para endereço manual.');
      return;
    }
    if (modoLocal === 'manual' && !data.cidade) {
      Alert.alert('Localização', 'Preencha ao menos a cidade.');
      return;
    }

    setEnviando(true);
    try {
      await avistamentoService.publicar(
        {
          fotos,
          data: toISO(data.data, data.hora),
          lat: modoLocal === 'gps' ? lat ?? undefined : undefined,
          lng: modoLocal === 'gps' ? lng ?? undefined : undefined,
          endereco_texto: modoLocal === 'gps' ? enderecoGps ?? undefined : undefined,
          local_cep: modoLocal === 'manual' ? data.cep?.replace(/\D/g, '') : undefined,
          local_logradouro: modoLocal === 'manual' ? data.logradouro : undefined,
          local_numero: modoLocal === 'manual' ? data.numero : undefined,
          local_bairro: modoLocal === 'manual' ? data.bairro : undefined,
          local_cidade: modoLocal === 'manual' ? data.cidade : undefined,
          local_estado: modoLocal === 'manual' ? data.estado : undefined,
          condicao: data.condicao,
          acoes_tomadas: data.acoes_tomadas,
          especie: data.especie,
        },
        user!.id,
        user!.nome,
      );
      navigation.goBack();
    } catch (err: any) {
      const msg = err?.response?.data?.mensagem ?? err?.message ?? 'Erro ao publicar avistamento.';
      setSnack({ visible: true, msg });
    } finally { setEnviando(false); }
  }

  return (
    <View style={styles.root}>
      <ScrollView contentContainerStyle={styles.scroll} keyboardShouldPersistTaps="handled">

        <Secao titulo="Fotos do animal (opcional, até 5)">
          <View style={styles.fotosRow}>
            {fotos.map((uri, idx) => (
              <View key={idx} style={styles.fotoContainer}>
                <Image source={{ uri }} style={styles.fotoThumb} />
                <TouchableOpacity style={styles.fotoRemover} onPress={() => setFotos(f => f.filter((_, i) => i !== idx))}>
                  <Text style={styles.fotoRemoverLabel}>✕</Text>
                </TouchableOpacity>
              </View>
            ))}
            {fotos.length < 5 && (
              <View style={styles.fotosBotoes}>
                <TouchableOpacity style={styles.btnFoto} onPress={tirarFoto}>
                  <Text style={styles.btnFotoLabel}>📷</Text>
                  <Text style={styles.btnFotoSub}>Câmera</Text>
                </TouchableOpacity>
                <TouchableOpacity style={styles.btnFoto} onPress={escolherFoto}>
                  <Text style={styles.btnFotoLabel}>🖼️</Text>
                  <Text style={styles.btnFotoSub}>Galeria</Text>
                </TouchableOpacity>
              </View>
            )}
          </View>
        </Secao>

        <Secao titulo="Data e hora do encontro">
          <View style={styles.row}>
            <View style={{ flex: 2 }}>
              <Campo label="Data (DD/MM/AAAA)" error={errors.data?.message}>
                <Controller control={control} name="data"
                  render={({ field: { onChange, value } }) => (
                    <MaskInput value={value} onChangeText={onChange} mask={DATE_MASK}
                      style={styles.maskInput} keyboardType="numeric" />
                  )} />
              </Campo>
            </View>
            <View style={{ flex: 1, marginLeft: spacing.sm }}>
              <Campo label="Hora (HH:MM)" error={errors.hora?.message}>
                <Controller control={control} name="hora"
                  render={({ field: { onChange, value } }) => (
                    <MaskInput value={value} onChangeText={onChange} mask={TIME_MASK}
                      style={styles.maskInput} keyboardType="numeric" />
                  )} />
              </Campo>
            </View>
          </View>
        </Secao>

        <Secao titulo="Espécie (se identificável)">
          <Campo label="" error={errors.especie?.message}>
            <Controller control={control} name="especie"
              render={({ field: { onChange, value } }) => (
                <View style={styles.chips}>
                  {ESPECIES.map(e => (
                    <TouchableOpacity key={e.value}
                      style={[styles.chip, value === e.value && styles.chipAtivo]}
                      onPress={() => onChange(e.value)}>
                      <Text style={[styles.chipLabel, value === e.value && styles.chipLabelAtivo]}>{e.label}</Text>
                    </TouchableOpacity>
                  ))}
                </View>
              )} />
          </Campo>
        </Secao>

        <Secao titulo="Localização">
          <View style={styles.modoRow}>
            <TouchableOpacity
              style={[styles.modoBotao, modoLocal === 'gps' && styles.modoBotaoAtivo]}
              onPress={() => setModoLocal('gps')}>
              <Text style={[styles.modoLabel, modoLocal === 'gps' && styles.modoLabelAtivo]}>📍 GPS</Text>
            </TouchableOpacity>
            <TouchableOpacity
              style={[styles.modoBotao, modoLocal === 'manual' && styles.modoBotaoAtivo]}
              onPress={() => setModoLocal('manual')}>
              <Text style={[styles.modoLabel, modoLocal === 'manual' && styles.modoLabelAtivo]}>✏️ Manual</Text>
            </TouchableOpacity>
          </View>

          {modoLocal === 'gps' ? (
            <View style={styles.gpsArea}>
              <TouchableOpacity
                style={[styles.btnGps, gpsCarregando && styles.btnDisabled]}
                onPress={usarGPS}
                disabled={gpsCarregando}
                activeOpacity={0.8}>
                {gpsCarregando
                  ? <ActivityIndicator color={colors.white} size="small" />
                  : <Text style={styles.btnGpsLabel}>Usar minha localização atual</Text>}
              </TouchableOpacity>
              {enderecoGps && (
                <View style={styles.enderecoGps}>
                  <Text style={styles.enderecoGpsLabel}>📍 {enderecoGps}</Text>
                </View>
              )}
            </View>
          ) : (
            <View>
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
                <View style={{ flex: 1 }}>
                  <Campo label="Número">
                    <Controller control={control} name="numero"
                      render={({ field: { onChange, onBlur, value } }) => (
                        <TextInput mode="outlined" value={value ?? ''} onChangeText={onChange} onBlur={onBlur}
                          keyboardType="numeric" outlineColor={colors.border} activeOutlineColor={colors.primary}
                          style={styles.input} contentStyle={styles.inputContent} />
                      )} />
                  </Campo>
                </View>
                <View style={{ flex: 2, marginLeft: spacing.sm }}>
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
                <View style={{ flex: 2 }}>
                  <Campo label="Cidade">
                    <Controller control={control} name="cidade"
                      render={({ field: { onChange, onBlur, value } }) => (
                        <TextInput mode="outlined" value={value ?? ''} onChangeText={onChange} onBlur={onBlur}
                          outlineColor={colors.border} activeOutlineColor={colors.primary}
                          style={styles.input} contentStyle={styles.inputContent} />
                      )} />
                  </Campo>
                </View>
                <View style={{ flex: 1, marginLeft: spacing.sm }}>
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
            </View>
          )}
        </Secao>

        <Secao titulo="Situação do animal">
          <Campo label="Condição física" error={errors.condicao?.message}>
            <Controller control={control} name="condicao"
              render={({ field: { onChange, onBlur, value } }) => (
                <TextInput mode="outlined" value={value} onChangeText={onChange} onBlur={onBlur}
                  multiline numberOfLines={3} placeholder="Ex: Machucado, com feridas na pata..."
                  outlineColor={colors.border} activeOutlineColor={colors.primary}
                  style={styles.inputMultiline} contentStyle={styles.inputContent} />
              )} />
          </Campo>

          <Campo label="Ações imediatas tomadas" error={errors.acoes_tomadas?.message}>
            <Controller control={control} name="acoes_tomadas"
              render={({ field: { onChange, onBlur, value } }) => (
                <TextInput mode="outlined" value={value} onChangeText={onChange} onBlur={onBlur}
                  multiline numberOfLines={3} placeholder="Ex: Ofereci água, chamei a proteção animal..."
                  outlineColor={colors.border} activeOutlineColor={colors.primary}
                  style={styles.inputMultiline} contentStyle={styles.inputContent} />
              )} />
          </Campo>
        </Secao>

        <TouchableOpacity
          style={[styles.btnPublicar, enviando && styles.btnDisabled]}
          onPress={handleSubmit(onSubmit)}
          disabled={enviando}
          activeOpacity={0.85}>
          {enviando
            ? <ActivityIndicator color={colors.white} />
            : <Text style={styles.btnPublicarLabel}>Publicar avistamento</Text>}
        </TouchableOpacity>
      </ScrollView>

      <Snackbar
        visible={snack.visible}
        onDismiss={() => setSnack(s => ({ ...s, visible: false }))}
        duration={4000}
        style={{ backgroundColor: colors.error }}>
        {snack.msg}
      </Snackbar>
    </View>
  );
}

function Secao({ titulo, children }: { titulo: string; children: React.ReactNode }) {
  return (
    <View style={styles.secao}>
      {titulo ? <Text style={styles.secaoTitulo}>{titulo}</Text> : null}
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
  secao: { backgroundColor: colors.bg, borderRadius: 12, padding: spacing.md, marginBottom: spacing.md },
  secaoTitulo: {
    fontFamily: typography.fontFamily.titleBold, fontSize: typography.fontSize.md,
    color: colors.text, marginBottom: spacing.sm,
    borderLeftWidth: 3, borderLeftColor: colors.accent, paddingLeft: spacing.sm,
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
  row: { flexDirection: 'row' },
  chips: { flexDirection: 'row', flexWrap: 'wrap', gap: spacing.xs },
  chip: { borderWidth: 1, borderColor: colors.border, borderRadius: 20, paddingVertical: 6, paddingHorizontal: spacing.md },
  chipAtivo: { backgroundColor: colors.accent, borderColor: colors.accent },
  chipLabel: { fontFamily: typography.fontFamily.body, fontSize: typography.fontSize.sm, color: colors.text },
  chipLabelAtivo: { color: colors.white },
  fotosRow: { flexDirection: 'row', flexWrap: 'wrap', gap: spacing.sm },
  fotoContainer: { position: 'relative', width: 72, height: 72 },
  fotoThumb: { width: 72, height: 72, borderRadius: 8 },
  fotoRemover: {
    position: 'absolute', top: -6, right: -6,
    backgroundColor: colors.error, borderRadius: 10, width: 20, height: 20, alignItems: 'center', justifyContent: 'center',
  },
  fotoRemoverLabel: { color: colors.white, fontSize: 10, fontFamily: typography.fontFamily.bodyBold },
  fotosBotoes: { flexDirection: 'row', gap: spacing.sm },
  btnFoto: {
    width: 72, height: 72, borderRadius: 8,
    borderWidth: 2, borderColor: colors.border, borderStyle: 'dashed',
    alignItems: 'center', justifyContent: 'center',
  },
  btnFotoLabel: { fontSize: 22 },
  btnFotoSub: { fontFamily: typography.fontFamily.body, fontSize: 9, color: colors.secondary },
  modoRow: { flexDirection: 'row', gap: spacing.sm, marginBottom: spacing.md },
  modoBotao: {
    flex: 1, borderWidth: 1, borderColor: colors.border, borderRadius: 8,
    paddingVertical: spacing.sm, alignItems: 'center',
  },
  modoBotaoAtivo: { backgroundColor: colors.primary, borderColor: colors.primary },
  modoLabel: { fontFamily: typography.fontFamily.bodyBold, fontSize: typography.fontSize.sm, color: colors.text },
  modoLabelAtivo: { color: colors.white },
  gpsArea: { gap: spacing.sm },
  btnGps: {
    backgroundColor: colors.primary, borderRadius: 8,
    paddingVertical: spacing.sm, paddingHorizontal: spacing.md, alignItems: 'center',
  },
  btnGpsLabel: { fontFamily: typography.fontFamily.bodyBold, fontSize: typography.fontSize.sm, color: colors.white },
  btnDisabled: { backgroundColor: colors.border },
  enderecoGps: {
    backgroundColor: colors.bgMuted, borderRadius: 8, padding: spacing.sm,
    borderLeftWidth: 3, borderLeftColor: colors.primary,
  },
  enderecoGpsLabel: { fontFamily: typography.fontFamily.body, fontSize: typography.fontSize.sm, color: colors.text },
  btnPublicar: { backgroundColor: colors.accent, borderRadius: 10, paddingVertical: spacing.md, alignItems: 'center', marginTop: spacing.sm },
  btnPublicarLabel: { fontFamily: typography.fontFamily.bodyBold, fontSize: typography.fontSize.md, color: colors.white },
});
