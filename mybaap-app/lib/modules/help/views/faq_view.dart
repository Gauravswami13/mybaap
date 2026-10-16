import 'package:flutter/material.dart';
import 'package:mybaap/core/color_constant.dart';
import 'package:mybaap/core/common_widgets/green_app_bar.dart';
import 'package:mybaap/core/common_widgets/state_message.dart';
import 'package:mybaap/core/fonts_styles.dart';
import 'package:mybaap/modules/help/providers/content_provider.dart';
import 'package:provider/provider.dart';

class FaqView extends StatefulWidget {
  const FaqView({super.key});

  @override
  State<FaqView> createState() => _FaqViewState();
}

class _FaqViewState extends State<FaqView> {
  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      context.read<ContentProvider>().loadFaqs();
    });
  }

  @override
  Widget build(BuildContext context) {
    final state = context.watch<ContentProvider>();
    return Scaffold(
      backgroundColor: ColorConstant.bgCl,
      appBar: const GreenAppBar(title: 'FAQ'),
      body: state.loading
          ? const Center(child: CircularProgressIndicator(color: ColorConstant.primaryCl))
          : state.error != null
              ? StateMessage(title: 'Could not load FAQs', subtitle: state.error, onRetry: state.loadFaqs)
              : state.faqs.isEmpty
                  ? StateMessage(title: 'No questions yet', onRetry: state.loadFaqs)
                  : ListView.separated(
                      padding: const EdgeInsets.all(16),
                      itemCount: state.faqs.length,
                      separatorBuilder: (_, _) => const SizedBox(height: 10),
                      itemBuilder: (context, index) {
                        final item = state.faqs[index];
                        return Container(
                          decoration: BoxDecoration(
                            color: ColorConstant.surfaceCl,
                            borderRadius: BorderRadius.circular(16),
                            border: Border.all(color: ColorConstant.lightBorderCl),
                          ),
                          child: ExpansionTile(
                            shape: const Border(),
                            collapsedShape: const Border(),
                            iconColor: ColorConstant.lightGrayIcCl,
                            collapsedIconColor: ColorConstant.lightGrayIcCl,
                            title: Text(item.question, style: const TextStyle(fontFamily: FontsStyle.medium, fontSize: 15, color: ColorConstant.darkGrayCl)),
                            children: [
                              Padding(
                                padding: const EdgeInsets.fromLTRB(16, 0, 16, 16),
                                child: Align(
                                  alignment: Alignment.centerLeft,
                                  child: Text(item.answer, style: const TextStyle(color: ColorConstant.bodyTextCl, fontFamily: FontsStyle.regular)),
                                ),
                              ),
                            ],
                          ),
                        );
                      },
                    ),
    );
  }
}
