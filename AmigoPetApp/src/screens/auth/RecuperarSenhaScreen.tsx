import React, { useState } from 'react';
import {
  View, StyleSheet, KeyboardAvoidingView, Platform, ScrollView, TouchableOpacity,
} from 'react-native';
import { Text, TextInput, Button, Snackbar } from 'react-native-paper';
import { useForm, Controller } from 'react-hook-form';
import { z } from 'zod';
import { zodResolver } from '@hookform/resolvers/zod';
import { useNavigation } from '@react-navigation/native';
import { authService } from '../../api/services/authService';
import { colors } from '../../theme/colors';
import { spacing } from '../../theme/spacing';
import { typography } from '../../theme/typography';

const schema = z.object({
  email: z.string().email('E-mail inválido'),
});

type FormData = z.infer<typeof schema>;

export function RecuperarSenhaScreen() {
  const navigation = useNavigation();
  const [loading, setLoading] = useState(false);
  const [enviado, setEnviado] = useState(false);
  const [snack, setSnack] = useState({ visible: false, msg: '' });

  const { control, handleSubmit, formState: { errors } } = useForm<FormData>({
    resolver: zodResolver(schema),
    defaultValues: { email: '' },
  });

  async function onSubmit(data: FormData) {
    setLoading(true);
    try {
      await authService.recuperarSenha(data.email);
      setEnviado(true);
    } catch {
      setEnviado(true);
    } finally {
      setLoading(false);
    }
  }

  if (enviado) {
    return (
      <View style={styles.container}>
        <View style={styles.successCard}>
          <Text style={styles.successIcon}>📧</Text>
          <Text style={styles.successTitulo}>Verifique seu e-mail</Text>
          <Text style={styles.successMsg}>
            Se o e-mail informado estiver cadastrado, você receberá as instruções para redefinir sua senha.
          </Text>
          <TouchableOpacity style={styles.btnVoltar} onPress={() => navigation.goBack()}>
            <Text style={styles.btnVoltarLabel}>Voltar para o login</Text>
          </TouchableOpacity>
        </View>
      </View>
    );
  }

  return (
    <KeyboardAvoidingView
      style={styles.flex}
      behavior={Platform.OS === 'ios' ? 'padding' : undefined}
    >
      <ScrollView contentContainerStyle={styles.container} keyboardShouldPersistTaps="handled">
        <Text style={styles.titulo}>Recuperar Senha</Text>
        <Text style={styles.subtitulo}>
          Digite seu e-mail cadastrado. Se encontrarmos sua conta, enviaremos as instruções de recuperação.
        </Text>

        <Controller
          control={control}
          name="email"
          render={({ field: { onChange, onBlur, value } }) => (
            <TextInput
              label="E-mail"
              mode="outlined"
              keyboardType="email-address"
              autoCapitalize="none"
              value={value}
              onBlur={onBlur}
              onChangeText={onChange}
              error={!!errors.email}
              style={styles.input}
              outlineColor={colors.border}
              activeOutlineColor={colors.primary}
            />
          )}
        />
        {errors.email && <Text style={styles.errorText}>{errors.email.message}</Text>}

        <Button
          mode="contained"
          onPress={handleSubmit(onSubmit)}
          loading={loading}
          disabled={loading}
          style={styles.btn}
          contentStyle={styles.btnContent}
          buttonColor={colors.primary}
          labelStyle={styles.btnLabel}
        >
          Recuperar Senha
        </Button>

        <TouchableOpacity onPress={() => navigation.goBack()} style={styles.linkVoltar}>
          <Text style={styles.linkVoltarLabel}>Voltar para o login</Text>
        </TouchableOpacity>
      </ScrollView>

      <Snackbar
        visible={snack.visible}
        onDismiss={() => setSnack(s => ({ ...s, visible: false }))}
        duration={3500}
        style={{ backgroundColor: colors.error }}
      >
        {snack.msg}
      </Snackbar>
    </KeyboardAvoidingView>
  );
}

const styles = StyleSheet.create({
  flex: { flex: 1, backgroundColor: colors.bg },
  container: { flexGrow: 1, justifyContent: 'center', paddingHorizontal: spacing.lg, paddingVertical: spacing.xxl },
  titulo: { fontFamily: typography.fontFamily.titleBold, fontSize: typography.fontSize.xl, color: colors.text, marginBottom: spacing.sm },
  subtitulo: { fontFamily: typography.fontFamily.body, fontSize: typography.fontSize.md, color: colors.secondary, marginBottom: spacing.lg },
  input: { backgroundColor: colors.bg, marginBottom: spacing.xs },
  errorText: { fontFamily: typography.fontFamily.body, fontSize: typography.fontSize.sm, color: colors.error, marginBottom: spacing.sm },
  btn: { marginTop: spacing.md, borderRadius: 8 },
  btnContent: { height: 50 },
  btnLabel: { fontFamily: typography.fontFamily.titleBold, fontSize: typography.fontSize.md },
  linkVoltar: { alignItems: 'center', marginTop: spacing.lg },
  linkVoltarLabel: { fontFamily: typography.fontFamily.body, color: colors.primary, fontSize: typography.fontSize.md },
  successCard: { alignItems: 'center', backgroundColor: colors.bg, borderRadius: 12, padding: spacing.xl, elevation: 2, shadowColor: colors.black, shadowOffset: { width: 0, height: 2 }, shadowOpacity: 0.06, shadowRadius: 6 },
  successIcon: { fontSize: 48, marginBottom: spacing.md },
  successTitulo: { fontFamily: typography.fontFamily.titleBold, fontSize: typography.fontSize.xl, color: colors.text, marginBottom: spacing.sm, textAlign: 'center' },
  successMsg: { fontFamily: typography.fontFamily.body, fontSize: typography.fontSize.md, color: colors.secondary, textAlign: 'center', lineHeight: 22, marginBottom: spacing.xl },
  btnVoltar: { backgroundColor: colors.primary, borderRadius: 8, paddingVertical: spacing.md, paddingHorizontal: spacing.xl },
  btnVoltarLabel: { fontFamily: typography.fontFamily.bodyBold, fontSize: typography.fontSize.md, color: colors.white },
});
