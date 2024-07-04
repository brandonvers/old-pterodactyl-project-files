import React, { useEffect, useState } from 'react';
import { RouteComponentProps } from "react-router-dom";
import PageContentBlock from '@/components/elements/PageContentBlock';
import tw from 'twin.macro';
import FlashMessageRender from '@/components/FlashMessageRender';
import Spinner from '@/components/elements/Spinner';
import GreyRowBox from '@/components/elements/GreyRowBox';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { faBook } from '@fortawesome/free-solid-svg-icons';
import useFlash from '@/plugins/useFlash';
import useSWR from 'swr';
import TitledGreyBox from '@/components/elements/TitledGreyBox';
import MessageBox from '@/components/MessageBox';

import getKnowledgebasePage from '@/api/knowledgebase/getKnowledgebasePage';

export interface KnowledgebasePageResponse {
    questions: any[];
    categories: any[];
    settings: any[];
}

type Props = {
    id: string;
}

export default ({ match }: RouteComponentProps<Props>) => {

    var id = match.params.id;

    const { clearFlashes, clearAndAddHttpError } = useFlash();
    const { data, error, mutate } = useSWR<KnowledgebasePageResponse>([ id, '/knowledgebase' ], ($id) => getKnowledgebasePage($id));


    useEffect(() => {
        if (!error) {
            clearFlashes('knowledgebase');
        } else {
            clearAndAddHttpError({ key: 'knowledgebase', error });
        }
    });

    return (
        <PageContentBlock title={'Knowledgebase Questions'} css={tw`flex flex-wrap`}>
            <div css={tw`w-full`}>
                <FlashMessageRender byKey={'knowledgebase'} css={tw`mb-4`} />
            </div>
            {!data ?
                <div css={tw`w-full`}>
                    <Spinner size={'large'} centered />
                </div>
                :
                <>
                    {data.questions.length < 1 ?
                        <div css={tw`w-full`}>
                            <MessageBox type="info" title="Info">
                                There are no questions.
                            </MessageBox>
                        </div>
                        :
                        (data.questions.map((item, key) => (
                            <div css={tw`w-full lg:pl-4 pt-4`} key={key}>
                                <GreyRowBox  css={tw`mb-2`} key={key}>
                                    <div css={tw`hidden md:block`}>
                                        <FontAwesomeIcon icon={faBook} fixedWidth/>
                                    </div>
                                    <div css={tw`ml-8 text-center hidden md:block`}>
                                        <p css={tw`text-sm`}>{item.author}</p>
                                        <p css={tw`mt-1 text-2xs text-neutral-300 uppercase select-none`}>Author</p>
                                    </div>
                                    <div css={tw`flex-1 ml-4 text-center`}>
                                        <p css={tw`text-sm`}>{item.categoryname.name}</p>
                                        <p css={tw`mt-1 text-2xs text-neutral-300 uppercase select-none`}>Category</p>
                                    </div>
                                    <div css={tw`ml-8 text-center hidden md:block`}>
                                        <p css={tw`text-sm`}>{item.updated_at}</p>
                                        <p css={tw`mt-1 text-2xs text-neutral-300 uppercase select-none`}>Updated</p>
                                    </div>
                                </GreyRowBox>
                                <br></br>
                                <TitledGreyBox title={item.subject}>
                                    <div css={tw`px-1 py-2`}>
                                        <span dangerouslySetInnerHTML={{ __html: item.information }}></span>
                                    </div>
                                </TitledGreyBox>
                            </div>
                        )))
                    }
                </>
            }
        </PageContentBlock>
    );
};
